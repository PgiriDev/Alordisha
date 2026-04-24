<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Teacher\TeacherStudentController;


// ------------------------
// PUBLIC ROUTES
// ------------------------

Route::get('/', function () {
    return view('welcome');
});

// LOGIN ROUTES
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ------------------------
// ADMIN ROUTES
// ------------------------

Route::prefix('admin')
    ->middleware(['auth.session', 'isAdmin'])
    ->group(function () {

        // Admin Dashboard
        Route::get('/', [AdminController::class, 'dashboard'])
            ->name('admin.dashboard');

        // Manage Teachers
        Route::resource('teachers', TeacherController::class);

        // Manage Branches
        Route::resource('branches', BranchController::class)
            ->except(['show']);

        // Manage Subjects
        Route::resource('subjects', SubjectController::class)
            ->except(['show']);
    });


// ------------------------
// TEACHER ROUTES
// ------------------------

Route::prefix('teacher')
    ->middleware(['auth.session', 'isTeacher'])
    ->group(function () {

        // Teacher Dashboard (Controller-based)
        Route::get('/', [TeacherDashboardController::class, 'index'])
            ->name('teacher.dashboard');


        // Student Management
        Route::resource('students', TeacherStudentController::class);

        // Attendance System
        Route::get('attendance', [AttendanceController::class, 'index'])
            ->name('attendance.index');

        Route::post('attendance/load', [AttendanceController::class, 'loadStudents'])
            ->name('attendance.load');

        Route::post('attendance/save', [AttendanceController::class, 'save'])
            ->name('attendance.save');
        Route::post('attendance/old', [AttendanceController::class, 'viewOld'])
            ->name('attendance.old');
    });