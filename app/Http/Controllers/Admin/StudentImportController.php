<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Branch;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentImportController extends Controller
{
    /*=========================================================
        SHOW IMPORT FORM (Simplified)
    =========================================================*/
    public function formShow()
    {
        // Get all active teachers only
        $teachers = User::where('role', 'teacher')->where('status', 'active')->get();

        return view('admin.students.import', compact('teachers'));
    }

    /*=========================================================
        PROCESS IMPORT (Simplified - Names instead of IDs)
    =========================================================*/
    public function processImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
            'teacher_id' => 'required|integer|exists:users,id',
        ]);

        $teacher_id = (int)$request->teacher_id;
        $teacher = User::where('id', $teacher_id)->where('role', 'teacher')->firstOrFail();

        $path = $request->file('csv_file')->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->withErrors(['csv_file' => 'File upload failed.']);
        }

        $headerRow = fgetcsv($handle);
        if (!$headerRow) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'CSV file is empty.']);
        }

        $headers = $this->normalizeCsvHeaders($headerRow);
        $importMap = [];
        $rowIndex = 1;
        $errors = [];

        // Parse all rows into import map
        while (($row = fgetcsv($handle)) !== false) {
            $rowIndex++;
            if ($this->isCsvRowEmpty($row)) {
                continue;
            }

            $rowData = $this->mapCsvRow($headers, $row);
            $name = $rowData['name'] ?? '';
            $phone = $rowData['phone'] ?? '';

            // Validate mandatory fields
            if ($name === '' || $phone === '') {
                $errors[] = "Row {$rowIndex}: missing name or phone.";
                continue;
            }

            // Resolve branch by name
            $branchName = $rowData['branch'] ?? '';
            if ($branchName === '') {
                $errors[] = "Row {$rowIndex}: missing branch.";
                continue;
            }

            $branch = Branch::where('name', trim($branchName))->first();
            if (!$branch) {
                $errors[] = "Row {$rowIndex}: branch '{$branchName}' not found.";
                continue;
            }

            // Resolve subject by name
            $subjectName = $rowData['subject'] ?? '';
            if ($subjectName === '') {
                $errors[] = "Row {$rowIndex}: missing subject.";
                continue;
            }

            $subject = Subject::where('name', trim($subjectName))->first();
            if (!$subject) {
                $errors[] = "Row {$rowIndex}: subject '{$subjectName}' not found.";
                continue;
            }

            // Get year label
            $yearLabel = $rowData['subject_year'] ?? '';
            if ($yearLabel === '') {
                $errors[] = "Row {$rowIndex}: missing subject_year.";
                continue;
            }

            $key = $this->buildStudentKey($name, $phone);
            
            // Create import entry
            $entry = [
                'fields' => [
                    'name' => $name,
                    'father_name' => $rowData['father_name'] ?? null,
                    'phone' => $phone,
                    'whatsapp' => $rowData['whatsapp'] ?? null,
                    'branch_id' => $branch->id,
                ],
                'subject_years' => [
                    [
                        'subject_id' => $subject->id,
                        'year_label' => (string)trim($yearLabel),
                    ]
                ],
            ];

            $importMap[$key] = $entry;
        }

        fclose($handle);

        if (empty($importMap)) {
            return back()->withErrors(['csv_file' => 'No valid rows found in CSV.'])
                        ->with('import_errors', $errors);
        }

        // Execute import with automatic merge
        return $this->executeImport($importMap, $teacher_id, $errors);
    }

    /*=========================================================
        EXECUTE IMPORT (Actual DB Insert/Update + Automatic Merge)
    =========================================================*/
    private function executeImport($importMap, $teacher_id, $errors)
    {
        $results = [
            'created' => 0,
            'merged' => 0,
            'errors' => $errors,
        ];

        try {
            DB::transaction(function () use ($importMap, $teacher_id, &$results) {
                foreach ($importMap as $key => $entry) {
                    if (!$entry['fields']['branch_id']) {
                        continue; // Skip if no branch
                    }

                    // Check if student already exists
                    $existing = Student::where('teacher_id', $teacher_id)
                        ->where('name', $entry['fields']['name'])
                        ->where('phone', $entry['fields']['phone'])
                        ->first();

                    if ($existing) {
                        // MERGE: Combine subjects + update empty fields
                        $allSubjectYears = (array)($existing->subject_years ?? []);
                        
                        foreach ($entry['subject_years'] as $newItem) {
                            $exists = false;
                            foreach ($allSubjectYears as $existingItem) {
                                if (($existingItem['subject_id'] ?? null) == $newItem['subject_id']
                                    && ($existingItem['year_label'] ?? '') === $newItem['year_label']) {
                                    $exists = true;
                                    break;
                                }
                            }
                            if (!$exists) {
                                $allSubjectYears[] = $newItem;
                            }
                        }

                        // Update empty fields only
                        foreach ($entry['fields'] as $field => $value) {
                            if ($value !== null && $value !== '' && empty($existing->{$field})) {
                                $existing->{$field} = $value;
                            }
                        }

                        $existing->subject_years = $allSubjectYears;
                        $existing->save();
                        
                        $results['merged']++;
                    } else {
                        // CREATE new student
                        $student = new Student();
                        foreach ($entry['fields'] as $field => $value) {
                            if ($value !== null) {
                                $student->{$field} = $value;
                            }
                        }
                        $student->subject_years = $entry['subject_years'];
                        $student->teacher_id = $teacher_id;
                        $student->status = 'active';
                        $student->save();
                        
                        $results['created']++;
                    }
                }
            });

            $message = "✓ Import completed! Created: {$results['created']}, Merged: {$results['merged']}.";

            if (!empty($results['errors'])) {
                return redirect()->route('admin.students')
                    ->with('success', $message)
                    ->with('import_errors', $results['errors']);
            }

            return redirect()->route('admin.students')
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    /*=========================================================
        CSV HELPER FUNCTIONS
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

    private function parseSubjectYears(array $rowData)
    {
        // Simplified: just return empty array as subjects are now handled in processImport
        return [];
    }

    private function parseDateValue($value)
    {
        if (!$value) return null;
        
        $value = trim($value);
        
        $formats = ['Y-m-d', 'd-m-Y', 'm/d/Y', 'd/m/Y'];
        
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date && $date->format($format) === $value) {
                return $date->format('Y-m-d');
            }
        }
        
        return null;
    }
}
