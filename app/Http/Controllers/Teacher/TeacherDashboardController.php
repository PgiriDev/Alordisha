<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Attendance;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $teacher = app('currentUser');   // logged-in teacher

        if(!$teacher){
            return "currentUser not found";
        }

        $totalStudents = Student::where('teacher_id', $teacher->id)->count();

        $activeStudents = Student::where('teacher_id', $teacher->id)
            ->where('status', 'active')
            ->count();

        $inactiveStudents = Student::where('teacher_id', $teacher->id)
            ->where('status', 'inactive')
            ->count();

        $totalSubjects = is_array($teacher->subjects)
                        ? count($teacher->subjects)
                        : 0;

        $totalBranches = is_array($teacher->branches)
                        ? count($teacher->branches)
                        : 0;

        $todayDate = now()->setTimezone('Asia/Kolkata')->toDateString();

        $attendanceToday = Attendance::where('teacher_id', $teacher->id)
                    ->whereDate('date', $todayDate)
                    ->distinct('student_id')
                    ->count('student_id');

        $activePercent = $totalStudents > 0
            ? (int) round(($activeStudents / $totalStudents) * 100)
            : 0;

        $attendanceBase = $activeStudents > 0 ? $activeStudents : $totalStudents;

        $attendancePercent = $attendanceBase > 0
            ? (int) min(100, round(($attendanceToday / $attendanceBase) * 100))
            : 0;

        return view('teacher.dashboard', [
            'totalStudents' => $totalStudents,
            'activeStudents' => $activeStudents,
            'inactiveStudents' => $inactiveStudents,
            'activePercent' => $activePercent,
            'attendancePercent' => $attendancePercent,
            'totalSubjects' => $totalSubjects,
            'totalBranches' => $totalBranches,
            'attendanceToday' => $attendanceToday,
        ]);
    }
}
