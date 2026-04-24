<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\FeeTrackerController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\NoticeController as AdminNoticeController;
use App\Http\Controllers\Admin\BookCollectionController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\Teacher\TeacherStudentController;
use App\Http\Controllers\PublicChatController;
use App\Models\User;


// ------------------------
// PUBLIC ROUTES
// ------------------------

Route::get('/', function () {
    $currentUser = null;

    if (session()->has('user_id')) {
        $currentUser = User::find(session('user_id'));
    }

    if (!$currentUser) {
        $rememberCookie = request()->cookie('remember_login');

        if (is_string($rememberCookie) && str_contains($rememberCookie, '|')) {
            [$rememberUserId, $rememberToken] = explode('|', $rememberCookie, 2);
            $rememberedUser = User::find($rememberUserId);

            if (
                $rememberedUser
                && $rememberedUser->status === 'active'
                && is_string($rememberedUser->remember_token)
                && hash_equals($rememberedUser->remember_token, (string) $rememberToken)
            ) {
                session([
                    'user_id' => $rememberedUser->id,
                    'role' => $rememberedUser->role,
                    'currentUser' => $rememberedUser,
                ]);

                $currentUser = $rememberedUser;
            }
        }
    }

    if ($currentUser) {
        return $currentUser->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('teacher.dashboard');
    }

    return view('welcome');
});

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy.policy');

Route::get('/notices/{notice}/download', [NoticeController::class, 'download'])
    ->name('notices.download');

Route::post('/chatbot/ask', [PublicChatController::class, 'ask'])
    ->name('chatbot.ask');

// LOGIN ROUTES
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
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

        // Teacher-wise Students (Admin view)
        Route::get('students', [AdminController::class, 'students'])
            ->name('admin.students');
        Route::get('students/transfer', [AdminController::class, 'transferStudentsForm'])
            ->name('admin.students.transfer.form');
        Route::get('students/{student}', [AdminController::class, 'studentShow'])
            ->name('admin.students.show');
        Route::post('students/transfer', [AdminController::class, 'transferStudents'])
            ->name('admin.students.transfer');

        // Bulk Import Students
        Route::get('students/import/form', [\App\Http\Controllers\Admin\StudentImportController::class, 'formShow'])
            ->name('admin.students.import');
        Route::post('students/import/process', [\App\Http\Controllers\Admin\StudentImportController::class, 'processImport'])
            ->name('admin.students.import.process');


        // Manage Teachers
        Route::resource('teachers', TeacherController::class);

        // Manage Branches
        Route::resource('branches', BranchController::class)
            ->except(['show']);

        // Manage Subjects
        Route::resource('subjects', SubjectController::class)
            ->except(['show']);

        // Manage Notices
        Route::get('notices', [AdminNoticeController::class, 'index'])
            ->name('admin.notices.index');
        Route::post('notices', [AdminNoticeController::class, 'store'])
            ->name('admin.notices.store');
        Route::delete('notices/{notice}', [AdminNoticeController::class, 'destroy'])
            ->name('admin.notices.destroy');

        // Book Collections
        Route::get('collections', [BookCollectionController::class, 'index'])
            ->name('admin.collections.index');
        Route::post('collections', [BookCollectionController::class, 'store'])
            ->name('admin.collections.store');
        Route::put('collections/{collection}', [BookCollectionController::class, 'update'])
            ->name('admin.collections.update');
        Route::delete('collections/{collection}', [BookCollectionController::class, 'destroy'])
            ->name('admin.collections.destroy');

        // Teacher media temp upload
        Route::post('upload-temp-file', [TeacherController::class, 'uploadTempFile'])
            ->name('admin.upload.temp');
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


        // ------------------------------------------------
        // Student Management
        // ------------------------------------------------

        Route::get('students/import', [TeacherStudentController::class, 'importForm'])
            ->name('students.import');

        Route::post('students/import', [TeacherStudentController::class, 'importStore'])
            ->name('students.import.store');

        Route::get('students/download-template', [TeacherStudentController::class, 'downloadTemplate'])
            ->name('students.download-template');

        Route::post('students/merge-duplicates', [TeacherStudentController::class, 'mergeDuplicates'])
            ->name('students.merge-duplicates');

        // Fees Tracker
        Route::get('fees-tracker', [FeeTrackerController::class, 'index'])
            ->name('fees.index');
        Route::post('fees-tracker/mark-paid', [FeeTrackerController::class, 'markPaid'])
            ->name('fees.mark-paid');
        Route::post('fees-tracker/mark-paid-multiple', [FeeTrackerController::class, 'markPaidMultiple'])
            ->name('fees.mark-paid-multiple');
        Route::get('fees-tracker/settings', [FeeTrackerController::class, 'settings'])
            ->name('fees.settings');
        Route::post('fees-tracker/settings/fees', [FeeTrackerController::class, 'saveFees'])
            ->name('fees.settings.save');
        Route::post('fees-tracker/settings/templates', [FeeTrackerController::class, 'saveTemplates'])
            ->name('fees.templates.save');
        Route::post('fees-tracker/settings/logo', [FeeTrackerController::class, 'saveLogo'])
            ->name('fees.logo.save');
        Route::get('fees-tracker/receipt/{payment}', [FeeTrackerController::class, 'receipt'])
            ->name('fees.receipt');
        Route::get('fees-tracker/report', [FeeTrackerController::class, 'report'])
            ->name('fees.report');
        Route::get('fees-tracker/report/pdf', [FeeTrackerController::class, 'reportPdf'])
            ->name('fees.report.pdf');

        // NEW: Temporary File Upload (AJAX) for Photos/Aadhaar
        Route::post('/upload-temp-file', [TeacherStudentController::class, 'uploadTempFile'])
            ->name('upload.temp');

        // Standard CRUD for Students
        Route::resource('students', TeacherStudentController::class);


        // ------------------------------------------------
        // Attendance System
        // ------------------------------------------------
        Route::get('attendance', [AttendanceController::class, 'index'])
            ->name('attendance.index');

        Route::post('attendance/load', [AttendanceController::class, 'loadStudents'])
            ->name('attendance.load');

        Route::post('attendance/save', [AttendanceController::class, 'save'])
            ->name('attendance.save');

        Route::post('attendance/old', [AttendanceController::class, 'viewOld'])
            ->name('attendance.old');
    });

