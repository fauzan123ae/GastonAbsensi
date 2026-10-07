<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\QrScannerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\SessionParticipantController;
use App\Http\Controllers\Admin\SportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Member\MemberAttendanceController;
use App\Http\Controllers\Member\MemberDashboardController;
use App\Http\Controllers\Member\MemberPermissionController;
use App\Http\Controllers\Member\MemberProfileController;
use App\Http\Controllers\Member\MemberQrController;
use App\Http\Controllers\Member\MemberSessionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\AdminProfileController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

// Protected Permission Proof Route
Route::get('/permissions/{permission}/proof', [\App\Http\Controllers\PermissionProofController::class, 'show'])
    ->middleware('auth')
    ->name('permissions.proof');

// ================= ADMIN ROUTES =================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [\App\Http\Controllers\Admin\GlobalSearchController::class, 'search'])->name('search');

    // Members Management
    Route::resource('members', MemberController::class)->except(['destroy']);
    Route::post('/members/{member}/toggle-status', [MemberController::class, 'toggleStatus'])->name('members.toggle-status');
    Route::get('/members/{member}/qr', [MemberController::class, 'qr'])->name('members.qr');

    // Sports & Locations
    Route::resource('sports', SportController::class)->except(['create', 'show', 'edit']);
    Route::resource('locations', LocationController::class)->except(['create', 'show', 'edit']);

    // Sessions Management
    Route::resource('sessions', SessionController::class)->except(['destroy']);
    Route::post('/sessions/{session}/status', [SessionController::class, 'updateStatus'])->name('sessions.update-status');

    // Session Participants
    Route::get('/sessions/{session}/participants', [SessionParticipantController::class, 'index'])->name('sessions.participants');
    Route::post('/sessions/{session}/participants', [SessionParticipantController::class, 'store'])->name('sessions.participants.store');
    Route::delete('/sessions/{session}/participants/{participant}', [SessionParticipantController::class, 'destroy'])->name('sessions.participants.destroy');
    Route::post('/sessions/{session}/participants/import', [SessionParticipantController::class, 'import'])->name('sessions.participants.import');

    // Session Attendance & Scanner
    Route::get('/sessions/{session}/attendance', [AttendanceController::class, 'index'])->name('sessions.attendance');
    Route::post('/sessions/{session}/attendance/manual', [AttendanceController::class, 'manualStore'])->name('sessions.attendance.manual');
    Route::post('/sessions/{session}/attendance/{attendance}/cancel', [AttendanceController::class, 'cancel'])->name('sessions.attendance.cancel');
    Route::post('/sessions/{session}/close', [AttendanceController::class, 'closeSession'])->name('sessions.close');

    Route::get('/sessions/{session}/scan', [QrScannerController::class, 'show'])->name('sessions.scan');
    Route::post('/sessions/{session}/scan/lookup', [QrScannerController::class, 'scan'])->name('sessions.scan.lookup');
    Route::post('/sessions/{session}/scan/confirm', [QrScannerController::class, 'confirm'])->name('sessions.scan.confirm');

    // Permissions Review
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::patch('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');

    // Reports & CSV Export
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // Admin & Master Admin Management (Master Admin Only)
    Route::resource('admins', \App\Http\Controllers\Admin\AdminManagementController::class)->except(['show']);
    Route::post('/admins/{admin}/toggle-status', [\App\Http\Controllers\Admin\AdminManagementController::class, 'toggleStatus'])->name('admins.toggle-status');

    // Admin Profile & Password Change
    Route::get('/profile', [AdminProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
});

// ================= MEMBER ROUTES =================
Route::prefix('member')->name('member.')->middleware(['auth', 'role:member'])->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
    Route::get('/qr', [MemberQrController::class, 'show'])->name('qr');
    Route::get('/sessions', [MemberSessionController::class, 'index'])->name('sessions.index');
    Route::get('/attendance', [MemberAttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/permissions', [MemberPermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions', [MemberPermissionController::class, 'store'])->name('permissions.store');
    Route::delete('/permissions/{permission}', [MemberPermissionController::class, 'destroy'])->name('permissions.destroy');
    Route::get('/profile', [MemberProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [MemberProfileController::class, 'update'])->name('profile.update');
});
