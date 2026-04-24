<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Branch;
use App\Models\Subject;
use App\Models\Attendance;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TeacherStudentController extends Controller
{
    /*=========================================================
        SHOW ALL STUDENTS FOR TEACHER (YOUR WORKING CODE)
    =========================================================*/
    public function index()
    {
        $user = app('currentUser');

        $students = Student::where('teacher_id', $user->id)
            ->with('branch')
            ->orderBy('name')
            ->get();

        foreach ($students as $student) {
            $total = Attendance::where('student_id', $student->id)->count();
            $present = Attendance::where('student_id', $student->id)
                ->where('status', 'Present')->count();

            $student->attendance_percentage = $total > 0
                ? round(($present / $total) * 100)
                : 0;
        }

        // Get duplicate students (name + phone match)
        $duplicates = $this->getDuplicateStudents($user->id);

        $subjectNameMap = Subject::pluck('name', 'id')->toArray();

        return view('teacher.students.index', compact('students', 'duplicates', 'subjectNameMap'));
    }

    /*=========================================================
        GET DUPLICATE STUDENTS (by name+phone)
    =========================================================*/
    private function getDuplicateStudents($teacherId)
    {
        // Get groups of students with same name+phone that have more than 1 record
        $duplicateGroups = Student::where('teacher_id', $teacherId)
            ->selectRaw('LOWER(name) as name_lower, LOWER(phone) as phone_lower, COUNT(*) as cnt')
            ->groupBy('name_lower', 'phone_lower')
            ->having('cnt', '>', 1)
            ->get();

        $result = [];
        foreach ($duplicateGroups as $group) {
            // Get actual students in this group
            $students = Student::where('teacher_id', $teacherId)
                ->whereRaw('LOWER(name) = ?', [strtolower($group->name_lower)])
                ->whereRaw('LOWER(phone) = ?', [strtolower($group->phone_lower)])
                ->orderBy('created_at', 'desc')
                ->with('branch')
                ->get();

            if ($students->count() > 1) {
                $result[] = [
                    'name' => $students->first()->name,
                    'phone' => $students->first()->phone,
                    'students' => $students,
                    'count' => $students->count(),
                ];
            }
        }

        return $result;
    }

    /*=========================================================
        MERGE DUPLICATE STUDENTS
    =========================================================*/
    public function mergeDuplicates(Request $request)
    {
        $request->validate([
            'keep_id' => 'required|integer|exists:students,id',
            'merge_ids' => 'required|array',
            'merge_ids.*' => 'integer|exists:students,id',
        ]);

        $user = app('currentUser');
        $keepId = (int)$request->keep_id;
        $mergeIds = array_map('intval', $request->merge_ids);

        // Verify all students belong to current teacher
        $keepStudent = Student::where('teacher_id', $user->id)->findOrFail($keepId);
        $mergeStudents = Student::where('teacher_id', $user->id)
            ->whereIn('id', $mergeIds)
            ->get();

        if ($mergeStudents->count() !== count($mergeIds)) {
            return back()->withErrors(['error' => 'Invalid students to merge.']);
        }

        DB::transaction(function () use ($keepStudent, $mergeStudents) {
            // Merge subject_years array
            $allSubjectYears = (array)($keepStudent->subject_years ?? []);

            foreach ($mergeStudents as $student) {
                $mergeYears = (array)($student->subject_years ?? []);
                foreach ($mergeYears as $item) {
                    $exists = false;
                    foreach ($allSubjectYears as $existingItem) {
                        if (($existingItem['subject_id'] ?? null) == ($item['subject_id'] ?? null)
                            && (string)($existingItem['year_label'] ?? '') === (string)($item['year_label'] ?? '')) {
                            $exists = true;
                            break;
                        }
                    }
                    if (!$exists) {
                        $allSubjectYears[] = $item;
                    }
                }

                // Delete merge student
                $student->delete();
            }

            $keepStudent->subject_years = $allSubjectYears;
            $keepStudent->save();
        });

        return redirect()->route('students.index')
            ->with('success', 'Students merged successfully.');
    }

    /*=========================================================
        SHOW CSV IMPORT FORM
    =========================================================*/
    public function importForm()
    {
        $user = app('currentUser');
        
        $branchIds = (array)($user->branches ?? []);
        $subjectIds = (array)($user->subjects ?? []);

        $branches = Branch::whereIn('id', $branchIds)->get();
        $subjects = Subject::whereIn('id', $subjectIds)->get();
        
        // Always show full year label list in dropdown
        $yearLabels = [
            'PP-1',
            'PP-2',
            'PR-1',
            '1ST',
            '2ND',
            '3RD',
            '4TH',
            '5TH',
            '6TH',
            '7TH',
            'KISHALAY-1',
            'KISHALAY-2',
            'SAHAJ PATH-1',
            'SAHAJ PATH-2',
        ];

        return view('teacher.students.import', compact('branches', 'subjects', 'yearLabels'));
    }

    /*=========================================================
        DOWNLOAD CSV TEMPLATE
    =========================================================*/
    public function downloadTemplate()
    {
        $user = app('currentUser');
        
        $branchIds = (array)($user->branches ?? []);
        $subjectIds = (array)($user->subjects ?? []);

        $branches = Branch::whereIn('id', $branchIds)->get();
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        $branchList = $branches->pluck('name', 'id')->all();
        $subjectList = $subjects->pluck('name', 'id')->all();

        $headers = [
            'name',
            'phone',
            'branch_id',
            'subject_id',
            'year_label',
            'father_name',
            'dob',
            'class_level',
            'whatsapp',
            'address',
            'institution',
            'registration_number',
        ];

        $rows = [];

        if (count($branchList) > 0 && count($subjectList) > 0) {
            $firstBranchId = key($branchList);
            $firstSubjectId = key($subjectList);

            $rows[] = [
                'Rahim Ahmed',
                '017XXXXXXXX',
                $firstBranchId,
                $firstSubjectId,
                '2024',
                'Abdul Ahmed',
                '2012-03-15',
                'Class 5',
                '017YYYYYYYY',
                'Mirpur, Dhaka',
                'MVKC',
                'REG-001',
            ];
        }

        $filename = 'students_import_template_' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    /*=========================================================
        IMPORT STUDENTS FROM CSV (SIMPLIFIED)
    =========================================================*/
    public function importStore(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'branch_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'year_label' => 'required|string',
        ]);

        $user = app('currentUser');
        $branchIds = array_map('intval', (array)($user->branches ?? []));
        $subjectIds = array_map('intval', (array)($user->subjects ?? []));

        // Validate selected branch and subject
        $branch_id = (int)$request->branch_id;
        $subject_id = (int)$request->subject_id;
        $year_label = (string)$request->year_label;

        if (!in_array($branch_id, $branchIds, true)) {
            return back()->withErrors(['branch_id' => 'Invalid branch selected.']);
        }
        if (!in_array($subject_id, $subjectIds, true)) {
            return back()->withErrors(['subject_id' => 'Invalid subject selected.']);
        }

        $path = $request->file('csv_file')->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->withErrors(['csv_file' => 'Failed to open CSV file.']);
        }

        $headerRow = fgetcsv($handle);
        if (!$headerRow) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'CSV file is empty.']);
        }

        $headers = $this->normalizeCsvHeaders($headerRow);
        $results = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        $importMap = [];
        $rowIndex = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowIndex++;
            if ($this->isCsvRowEmpty($row)) {
                continue;
            }

            $rowData = $this->mapCsvRow($headers, $row);

            $name = $rowData['name'] ?? '';
            $phone = $rowData['phone'] ?? '';

            if ($name === '' || $phone === '') {
                $results['skipped']++;
                $results['errors'][] = "Row {$rowIndex}: missing name or phone.";
                continue;
            }

            $key = $this->buildStudentKey($name, $phone);
            $entry = $importMap[$key] ?? [
                'fields' => [
                    'name' => $name,
                    'father_name' => $rowData['father_name'] ?? null,
                    'dob' => $this->parseDateValue($rowData['dob'] ?? null),
                    'class_level' => $rowData['class_level'] ?? null,
                    'phone' => $phone,
                    'whatsapp' => $rowData['whatsapp'] ?? null,
                    'address' => $rowData['address'] ?? null,
                    'institution' => $rowData['institution'] ?? null,
                    'registration_number' => $rowData['registration_number'] ?? null,
                    'branch_id' => $branch_id,
                ],
                'subject_years' => [],
            ];

            // Add subject+year to this student (from dropdown)
            $entry['subject_years'][] = [
                'subject_id' => $subject_id,
                'year_label' => $year_label,
            ];

            $importMap[$key] = $entry;
        }

        fclose($handle);

        DB::transaction(function () use ($importMap, $user, &$results) {
            foreach ($importMap as $entry) {
                $fields = $entry['fields'];
                $subjectYears = $entry['subject_years'];

                $student = Student::where('teacher_id', $user->id)
                    ->where('name', $fields['name'])
                    ->where('phone', $fields['phone'])
                    ->first();

                if ($student) {
                    // Update existing
                    foreach ($fields as $field => $value) {
                        if ($value !== null && $value !== '') {
                            $student->{$field} = $value;
                        }
                    }

                    $student->subject_years = $this->mergeSubjectYears(
                        (array)($student->subject_years ?? []),
                        $subjectYears
                    );

                    $student->save();
                    $results['updated']++;
                    continue;
                }

                // Create new
                $newStudent = new Student();
                foreach ($fields as $field => $value) {
                    $newStudent->{$field} = $value;
                }
                $newStudent->subject_years = $subjectYears;
                $newStudent->teacher_id = $user->id;
                $newStudent->status = 'active';
                $newStudent->save();
                $results['created']++;
            }
        });

        $message = "Import done. Created: {$results['created']}, Updated: {$results['updated']}, Skipped: {$results['skipped']}.";

        if (!empty($results['errors'])) {
            $message .= ' Some rows were skipped.';
        }

        return redirect()->route('students.import')
            ->with('success', $message)
            ->with('import_errors', $results['errors']);
    }

    /*=========================================================
        SHOW CREATE FORM
    =========================================================*/
    public function create()
    {
        $user = app('currentUser');

        // SAFETY FIX: Force these to be arrays to prevent 500 Error
        $branchIds = (array)($user->branches ?? []);
        $subjectIds = (array)($user->subjects ?? []);

        $branches = Branch::whereIn('id', $branchIds)->get();
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        return view('teacher.students.create', compact('branches', 'subjects'));
    }

    /*=========================================================
        STORE NEW STUDENT (With Temp File Move)
    =========================================================*/
    public function store(Request $r)
    {
        $user = app('currentUser');

        $r->validate([
            'name' => 'required',
            'branch_id' => 'required|exists:branches,id',
            'subject_years' => 'required|array',
            'dob' => 'nullable|date',
            'photo_path' => 'nullable|string', 
            'aadhaar_path' => 'nullable|string',
        ]);

        $student = new Student();
        $student->name = $r->name;
        $student->father_name = $r->father_name;
        $student->dob = $r->dob;
        $student->class_level = $r->class_level;
        $student->phone = $r->phone;
        $student->whatsapp = $r->whatsapp;
        $student->address = $r->address;
        $student->institution = $r->institution;

        // HANDLE PHOTO: Move from temp folder to permanent folder
        if ($r->filled('photo_path')) {
            $student->photo_path = $this->moveFileFromTemp($r->photo_path, 'students/photos');
        }

        // HANDLE AADHAAR
        if ($r->filled('aadhaar_path')) {
            $student->aadhaar_path = $this->moveFileFromTemp($r->aadhaar_path, 'students/aadhaar');
        }

        $student->subject_years = $r->subject_years;
        $student->branch_id = $r->branch_id;
        $student->teacher_id = $user->id;
        $student->status = 'active';

        $student->save();

        return redirect()->route('students.index')
            ->with('success', 'Student registered successfully.');
    }

    /*=========================================================
        EDIT STUDENT (FIXED)
    =========================================================*/
    public function edit($id)
    {
        $user = app('currentUser');

        // Find student belonging to THIS teacher
        $student = Student::where('teacher_id', $user->id)->findOrFail($id);

        // SAFETY FIX: Force to array to prevent 500 error
        $branchIds = (array)($user->branches ?? []);
        $subjectIds = (array)($user->subjects ?? []);

        $branches = Branch::whereIn('id', $branchIds)->get();
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        return view('teacher.students.edit', compact('student', 'branches', 'subjects'));
    }

    /*=========================================================
        UPDATE STUDENT (With Temp File Move)
    =========================================================*/
    public function update(Request $r, $id)
    {
        $user = app('currentUser');
        $student = Student::where('teacher_id', $user->id)->findOrFail($id);

        $r->validate([
            'name' => 'required',
            'branch_id' => 'required|exists:branches,id',
            'subject_years' => 'required|array',
        ]);

        $student->name = $r->name;
        $student->father_name = $r->father_name;
        $student->dob = $r->dob;
        $student->class_level = $r->class_level;
        $student->phone = $r->phone;
        $student->whatsapp = $r->whatsapp;
        $student->address = $r->address;
        $student->institution = $r->institution;
        $student->branch_id = $r->branch_id;
        $student->subject_years = $r->subject_years;
        $student->status = $r->status;

        // UPDATE PHOTO
        if ($r->filled('photo_path') && $r->photo_path !== $student->photo_path) {
            // Delete old file if it exists and is not the same as new
            if ($student->photo_path) {
                Storage::disk('public')->delete($student->photo_path);
            }
            $student->photo_path = $this->moveFileFromTemp($r->photo_path, 'students/photos');
        }

        // UPDATE AADHAAR
        if ($r->filled('aadhaar_path') && $r->aadhaar_path !== $student->aadhaar_path) {
            if ($student->aadhaar_path) {
                Storage::disk('public')->delete($student->aadhaar_path);
            }
            $student->aadhaar_path = $this->moveFileFromTemp($r->aadhaar_path, 'students/aadhaar');
        }

        $student->save();

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    /*=========================================================
        DELETE STUDENT
    =========================================================*/
    public function destroy($id)
    {
        $user = app('currentUser');

        $student = Student::where('teacher_id', $user->id)->findOrFail($id);

        if ($student->photo_path) Storage::disk('public')->delete($student->photo_path);
        if ($student->aadhaar_path) Storage::disk('public')->delete($student->aadhaar_path);

        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /*=========================================================
        UPLOAD TEMP FILE (This handles the AJAX upload)
    =========================================================*/
    public function uploadTempFile(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('temp', 'public');
            return response()->json(['path' => $path]);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    /*=========================================================
        HELPER: MOVE FILE FROM TEMP TO PERMANENT
    =========================================================*/
    private function moveFileFromTemp($tempPath, $targetFolder)
    {
        // 1. Check if it's a temp path
        // 2. Check if file actually exists
        if (strpos($tempPath, 'temp/') !== false && Storage::disk('public')->exists($tempPath)) {
            $fileName = basename($tempPath);
            $newPath = $targetFolder . '/' . $fileName;
            
            // Move it
            Storage::disk('public')->move($tempPath, $newPath);
            
            return $newPath;
        }
        
        // If it's already a permanent path (or missing), just return it as is
        return $tempPath;
    }

    /*=========================================================
        CSV HELPERS
    =========================================================*/
    private function normalizeCsvHeaders(array $headers)
    {
        $normalized = [];
        foreach ($headers as $header) {
            $key = trim($header);
            $key = preg_replace('/^\xEF\xBB\xBF/', '', $key);
            $key = strtolower($key);
            $key = preg_replace('/[^a-z0-9]+/', '_', $key);
            $key = trim($key, '_');
            $normalized[] = $key;
        }

        return $normalized;
    }

    private function mapCsvRow(array $headers, array $row)
    {
        $row = array_pad($row, count($headers), '');
        $row = array_slice($row, 0, count($headers));

        $mapped = [];
        foreach ($headers as $index => $header) {
            $mapped[$header] = trim((string)($row[$index] ?? ''));
        }

        return $mapped;
    }

    private function isCsvRowEmpty(array $row)
    {
        foreach ($row as $value) {
            if (trim((string)$value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function buildStudentKey($name, $phone)
    {
        $cleanPhone = preg_replace('/\D+/', '', (string)$phone);
        return strtolower(trim($name)) . '|' . $cleanPhone;
    }

    private function resolveBranchId(array $rowData, array $allowedBranchIds)
    {
        $branchId = isset($rowData['branch_id']) ? (int)$rowData['branch_id'] : 0;

        if (!$branchId && !empty($rowData['branch_name'])) {
            $branch = Branch::where('name', $rowData['branch_name'])->first();
            $branchId = $branch?->id ?? 0;
        }

        if (!empty($allowedBranchIds) && $branchId && !in_array($branchId, $allowedBranchIds, true)) {
            return 0;
        }

        return $branchId;
    }

    private function parseSubjectYears(array $rowData)
    {
        $subjectYears = [];

        if (!empty($rowData['subject_years'])) {
            $raw = trim($rowData['subject_years']);
            $decoded = json_decode($raw, true);

            if (is_array($decoded)) {
                foreach ($decoded as $item) {
                    if (!is_array($item)) {
                        continue;
                    }
                    $subjectId = isset($item['subject_id']) ? (int)$item['subject_id'] : 0;
                    $yearLabel = $item['year_label'] ?? null;
                    if ($subjectId && $yearLabel) {
                        $subjectYears[] = [
                            'subject_id' => $subjectId,
                            'year_label' => (string)$yearLabel,
                        ];
                    }
                }
            } else {
                $pairs = preg_split('/\s*\|\s*/', $raw);
                foreach ($pairs as $pair) {
                    if ($pair === '') {
                        continue;
                    }
                    $parts = array_map('trim', explode(':', $pair, 2));
                    $subjectId = isset($parts[0]) ? (int)$parts[0] : 0;
                    $yearLabel = $parts[1] ?? null;
                    if ($subjectId && $yearLabel) {
                        $subjectYears[] = [
                            'subject_id' => $subjectId,
                            'year_label' => $yearLabel,
                        ];
                    }
                }
            }
        }

        foreach ($rowData as $key => $value) {
            if (!preg_match('/^subject_id(_\d+)?$/', $key, $matches)) {
                continue;
            }

            $suffix = $matches[1] ?? '';
            $yearKey = 'year_label' . $suffix;
            $subjectId = (int)$value;
            $yearLabel = $rowData[$yearKey] ?? null;

            if ($subjectId && $yearLabel) {
                $subjectYears[] = [
                    'subject_id' => $subjectId,
                    'year_label' => $yearLabel,
                ];
            }
        }

        return $subjectYears;
    }

    private function filterSubjectYears(array $subjectYears, array $allowedSubjectIds)
    {
        if (empty($allowedSubjectIds)) {
            return $subjectYears;
        }

        return array_values(array_filter($subjectYears, function ($item) use ($allowedSubjectIds) {
            return isset($item['subject_id']) && in_array((int)$item['subject_id'], $allowedSubjectIds, true);
        }));
    }

    private function mergeSubjectYears(array $existing, array $incoming)
    {
        $merged = $existing;

        foreach ($incoming as $item) {
            $subjectId = $item['subject_id'] ?? null;
            $yearLabel = $item['year_label'] ?? null;
            if (!$subjectId || !$yearLabel) {
                continue;
            }

            $exists = false;
            foreach ($merged as $existingItem) {
                if (($existingItem['subject_id'] ?? null) == $subjectId
                    && (string)($existingItem['year_label'] ?? '') === (string)$yearLabel) {
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                $merged[] = [
                    'subject_id' => (int)$subjectId,
                    'year_label' => (string)$yearLabel,
                ];
            }
        }

        return $merged;
    }

    private function parseDateValue($value)
    {
        if (!$value) {
            return null;
        }

        $timestamp = strtotime($value);
        if (!$timestamp) {
            return null;
        }

        return date('Y-m-d', $timestamp);
    }
}