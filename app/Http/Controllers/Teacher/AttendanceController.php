<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\AttendanceSummaryMail;

class AttendanceController extends Controller
{
    private function studentHasSubject(Student $student, $subjectId): bool
    {
        $subjectYears = $student->subject_years;

        if (is_string($subjectYears)) {
            $subjectYears = json_decode($subjectYears, true);
        }

        if (!is_array($subjectYears)) {
            return false;
        }

        foreach ($subjectYears as $sy) {
            if (isset($sy['subject_id']) && (int) $sy['subject_id'] === (int) $subjectId) {
                return true;
            }
        }

        return false;
    }

    # --------------------------------------------------------
    # SHOW MAIN ATTENDANCE PAGE
    # --------------------------------------------------------
    public function index()
    {
        $teacher = User::find(session('user_id'));

        if (!$teacher) {
            return redirect()->route('login');
        }

        $branches = Branch::whereIn('id', $teacher->branches ?? [])->get();
        $subjects = Subject::whereIn('id', $teacher->subjects ?? [])->get();

        return view('teacher.attendance.index', compact('branches', 'subjects'));
    }

    # --------------------------------------------------------
    # LOAD STUDENTS (With Filter & Duplicate Fix)
    # --------------------------------------------------------
    public function loadStudents(Request $r)
    {
        $teacherId = session('user_id');

        // 1. Validation Check to prevent "Nested Website" error
        if (!$r->branch_id || !$r->subject_id || !$r->date) {
            return response('<div class="text-center p-5 border border-dashed rounded text-warning bg-opacity-10 bg-warning">
                                <i class="fa-solid fa-triangle-exclamation fa-2x mb-2"></i><br>
                                Please select <strong>Branch</strong>, <strong>Subject</strong>, and <strong>Date</strong>.
                             </div>');
        }

        // 2. Fetch students
        $allStudents = Student::where('branch_id', $r->branch_id)
            ->where('teacher_id', $teacherId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // 3. Filter students who have this specific Subject
        $students = $allStudents->filter(fn ($student) => $this->studentHasSubject($student, $r->subject_id));

        // 4. Load attendance
        $rows = Attendance::where('branch_id', $r->branch_id)
            ->where('teacher_id', $teacherId)
            ->where('subject_id', $r->subject_id)
            ->where('date', $r->date)
            ->orderByDesc('updated_at')
            ->get();

        $attendance = $rows->keyBy('student_id');

        return view('teacher.attendance.load', [
            'students'   => $students,
            'attendance' => $attendance,
            'branch_id'  => $r->branch_id,
            'subject_id' => $r->subject_id,
            'date'       => $r->date
        ]);
    }

    # --------------------------------------------------------
    # SAVE ATTENDANCE
    # --------------------------------------------------------
    public function save(Request $r)
    {
        $teacherId = session('user_id');
        $teacher = User::find($teacherId);

        if(empty($r->status)) {
             return back()->with('error', 'No data to save.');
        }

        $studentIds = array_map('intval', array_keys((array) $r->status));
        $studentsById = Student::whereIn('id', $studentIds)
            ->where('teacher_id', $teacherId)
            ->where('branch_id', $r->branch_id)
            ->where('status', 'active')
            ->get(['id', 'name', 'subject_years'])
            ->filter(fn ($student) => $this->studentHasSubject($student, $r->subject_id))
            ->keyBy('id');

        $attendanceRows = [];
        $presentNames = [];
        $absentNames = [];

        foreach ($r->status as $student_id => $status) {
            if (!$studentsById->has((int) $student_id)) {
                continue;
            }

            $status = ucfirst(strtolower(trim($status)));
            if (!in_array($status, ['Present', 'Absent'])) {
                $status = 'Absent';
            }

            Attendance::updateOrCreate(
                [
                    'teacher_id' => $teacherId,
                    'student_id' => $student_id,
                    'branch_id'  => $r->branch_id,
                    'subject_id' => $r->subject_id,
                    'date'       => $r->date,
                ],
                [
                    'status' => $status,
                    'time'   => now()->format('H:i:s'),
                ]
            );

            $studentName = $studentsById->get((int) $student_id)?->name ?? ('Student #' . $student_id);
            $attendanceRows[] = [
                'student_id' => (int) $student_id,
                'student_name' => $studentName,
                'status' => $status,
            ];

            if ($status === 'Present') {
                $presentNames[] = $studentName;
            } else {
                $absentNames[] = $studentName;
            }
        }

        if (empty($attendanceRows)) {
            return back()->with('error', 'No valid students found for this teacher, branch, and subject.');
        }

        if ($teacher && !empty($teacher->email)) {
            $teacherIdForMail = (int) $teacher->id;
            $branchId = (int) $r->branch_id;
            $subjectId = (int) $r->subject_id;
            $dateValue = (string) $r->date;
            $attendanceRowsForMail = $attendanceRows;
            $presentNamesForMail = $presentNames;
            $absentNamesForMail = $absentNames;

            dispatch(function () use (
                $teacherIdForMail,
                $branchId,
                $subjectId,
                $dateValue,
                $attendanceRowsForMail,
                $presentNamesForMail,
                $absentNamesForMail
            ) {
                try {
                    $freshTeacher = User::find($teacherIdForMail);

                    if (!$freshTeacher || empty($freshTeacher->email)) {
                        return;
                    }

                    $branchName = Branch::find($branchId)?->name ?? 'N/A';
                    $subjectName = Subject::find($subjectId)?->name ?? 'N/A';

                    $summary = [
                        'teacher_name' => $freshTeacher->name,
                        'branch_name' => $branchName,
                        'subject_name' => $subjectName,
                        'date' => $dateValue,
                        'submitted_at' => now()->format('d M Y, h:i A'),
                        'total_students' => count($attendanceRowsForMail),
                        'present_count' => count($presentNamesForMail),
                        'absent_count' => count($absentNamesForMail),
                        'present_names' => $presentNamesForMail,
                        'absent_names' => $absentNamesForMail,
                        'attendance_rows' => $attendanceRowsForMail,
                    ];

                    $pdfBinary = null;
                    $pdfFileName = null;

                    if (count($attendanceRowsForMail) > 50 && class_exists('Dompdf\\Dompdf')) {
                        $pdfHtml = view('emails.pdf.attendance-summary', [
                            'summary' => $summary,
                            'teacher' => $freshTeacher,
                        ])->render();

                        $dompdf = new \Dompdf\Dompdf();
                        $dompdf->setPaper('A4', 'portrait');
                        $dompdf->loadHtml($pdfHtml);
                        $dompdf->render();

                        $pdfBinary = $dompdf->output();
                        $pdfFileName = 'attendance-' . now()->format('Ymd-His') . '.pdf';
                    }

                    Mail::to($freshTeacher->email)->send(new AttendanceSummaryMail($summary, $freshTeacher, $pdfBinary, $pdfFileName));
                } catch (\Throwable $e) {
                    report($e);
                }
            })->afterResponse();
        }

        return back()->with('msg', 'Attendance Saved Successfully!');
    }

    # --------------------------------------------------------
    # VIEW HISTORY (With Totals & Duplicate Fix)
    # --------------------------------------------------------
    public function viewOld(Request $r)
    {
        $teacherId = session('user_id');

        // 1. Validation Check to prevent "Nested Website" error
        if (!$r->branch_id || !$r->subject_id || !$r->date) {
             return response('<div class="text-center p-3 text-muted">Please select all fields to view records.</div>');
        }

        $rows = Attendance::where('branch_id', $r->branch_id)
            ->where('teacher_id', $teacherId)
            ->where('subject_id', $r->subject_id)
            ->where('date', $r->date)
            ->with('student')
            ->orderByDesc('updated_at')
            ->get();

        $latest = $rows->unique('student_id')->values();

        return view('teacher.attendance.old', [
            'records' => $latest
        ]);
    }
}