<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Branch;
use App\Models\Subject;
use App\Models\Attendance;
use App\Models\BookCollection;
use App\Models\StudentTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Total counts
        $totalTeachers = \App\Models\User::where('role', 'teacher')->count();
        $totalStudents = \App\Models\Student::count();
        $totalBranches = \App\Models\Branch::count();
        $totalSubjects = \App\Models\Subject::count();
        $totalCollections = \App\Models\BookCollection::count();

        $activeStudents = \App\Models\Student::where('status', 'active')->count();
        $inactiveStudents = \App\Models\Student::where('status', 'inactive')->count();
        $activePercent = $totalStudents > 0
            ? (int) round(($activeStudents / $totalStudents) * 100)
            : 0;

        // Students per branch (important)
        $branch_students = \App\Models\Branch::withCount('students')->get();

        // Recent Students
        $recentStudents = \App\Models\Student::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalTeachers',
            'totalStudents',
            'totalBranches',
            'totalSubjects',
            'totalCollections',
            'activeStudents',
            'inactiveStudents',
            'activePercent',
            'branch_students',
            'recentStudents'
        ));
    }

    public function students(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $studentFilter = function ($query) use ($search) {
            if ($search === '') {
                return;
            }

            $like = '%' . $search . '%';

            $query->where(function ($subQuery) use ($like) {
                $subQuery->where('name', 'like', $like)
                    ->orWhere('father_name', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('whatsapp', 'like', $like)
                    ->orWhere('registration_number', 'like', $like)
                    ->orWhere('institution', 'like', $like)
                    ->orWhereHas('branch', function ($branchQuery) use ($like) {
                        $branchQuery->where('name', 'like', $like);
                    });
            });
        };

        $teachers = User::query()
            ->where('role', 'teacher')
            ->when($search !== '', function ($query) use ($studentFilter) {
                $query->whereHas('students', $studentFilter);
            })
            ->with(['students' => function ($query) use ($studentFilter) {
                $query->with('branch')
                    ->orderBy('name');

                $studentFilter($query);
            }])
            ->orderBy('name')
            ->get();

        $subjectIds = $teachers
            ->flatMap(fn ($teacher) => $teacher->students)
            ->flatMap(function ($student) {
                return collect((array) ($student->subject_years ?? []))
                    ->pluck('subject_id')
                    ->filter();
            })
            ->unique()
            ->values();

        $subjectMap = Subject::whereIn('id', $subjectIds)->pluck('name', 'id');

        $allStudents = $teachers->flatMap(fn ($teacher) => $teacher->students);

        $totalStudents = $allStudents->count();
        $totalActive = $allStudents->where('status', 'active')->count();
        $totalInactive = $allStudents->where('status', 'inactive')->count();

        $inactiveTeacherAlerts = User::query()
            ->where('role', 'teacher')
            ->where('status', 'inactive')
            ->withCount('students')
            ->having('students_count', '>', 0)
            ->orderByDesc('students_count')
            ->get(['id', 'name']);

        return view('admin.students', compact(
            'teachers',
            'totalStudents',
            'totalActive',
            'totalInactive',
            'subjectMap',
            'search',
            'inactiveTeacherAlerts'
        ));
    }

    public function transferStudentsForm()
    {
        $sourceTeachers = User::query()
            ->where('role', 'teacher')
            ->with(['students' => function ($query) {
                $query->select(['id', 'name', 'phone', 'teacher_id'])->orderBy('name');
            }])
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'status']);

        $targetTeachers = User::query()
            ->where('role', 'teacher')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'phone']);

        return view('admin.students_transfer', compact('sourceTeachers', 'targetTeachers'));
    }

    public function transferStudents(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', Rule::exists('students', 'id')],
            'target_teacher_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'teacher')->where('status', 'active');
                }),
            ],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $adminId = (int) session('user_id');
        $targetTeacherId = (int) $validated['target_teacher_id'];
        $reason = trim((string) ($validated['reason'] ?? ''));

        $students = Student::query()
            ->whereIn('id', $validated['student_ids'])
            ->get(['id', 'teacher_id']);

        if ($students->isEmpty()) {
            return back()->with('warning', 'No students found for transfer.');
        }

        $now = now();
        $transferLogs = [];
        $updatedCount = 0;

        DB::transaction(function () use ($students, $targetTeacherId, $reason, $adminId, $now, &$transferLogs, &$updatedCount) {
            foreach ($students as $student) {
                $fromTeacherId = (int) $student->teacher_id;

                if ($fromTeacherId === $targetTeacherId) {
                    continue;
                }

                $student->teacher_id = $targetTeacherId;
                $student->save();
                $updatedCount++;

                $transferLogs[] = [
                    'student_id' => $student->id,
                    'from_teacher_id' => $fromTeacherId,
                    'to_teacher_id' => $targetTeacherId,
                    'transferred_by' => $adminId,
                    'reason' => $reason !== '' ? $reason : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($transferLogs)) {
                StudentTransfer::insert($transferLogs);
            }
        });

        if ($updatedCount === 0) {
            return back()->with('warning', 'Selected students are already assigned to the target teacher.');
        }

        return back()->with('success', $updatedCount . ' student(s) transferred successfully.');
    }

    public function studentShow(Student $student)
    {
        $student->load(['branch', 'teacher']);

        $subjectIds = collect((array) ($student->subject_years ?? []))
            ->pluck('subject_id')
            ->filter()
            ->unique()
            ->values();

        $subjectMap = Subject::whereIn('id', $subjectIds)->pluck('name', 'id');

        return view('admin.students_show', compact('student', 'subjectMap'));
    }
}