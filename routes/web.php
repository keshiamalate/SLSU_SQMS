<?php
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\NotificationController as AdminNotif;
use App\Http\Controllers\Admin\ScholarshipController;
use App\Http\Controllers\Admin\ScholarshipCriteriaController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Auth\ConsentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\DocumentUploadController;
use App\Http\Controllers\Student\MatchingResultsController;
use App\Http\Controllers\Student\NotificationController as StudentNotif;
use App\Http\Controllers\Student\QuestionnaireController;
use App\Http\Controllers\Student\ScholarshipListingController;
use App\Http\Controllers\Student\ApplicationController as StudentApplication;
use Illuminate\Support\Facades\Route;

// Public routes
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLogin'])->name('login');
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login.form');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('/register', [RegisterController::class, 'show'])->name('register.show');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    // Forgot password
    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'send'])->name('password.email');

    // Reset password
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')->name('logout');

// Consent — auth only, NO consent middleware (prevents loop)
Route::middleware('auth')->group(function () {
    Route::get('/consent', [ConsentController::class, 'show'])->name('consent.show');
    Route::post('/consent', [ConsentController::class, 'store'])->name('consent.store');
});

// Questionnaire — after consent, before profile complete
Route::middleware(['auth', 'role:student', 'consent'])
    ->prefix('student')->name('student.')
    ->group(function () {
        Route::get('/questionnaire/{step?}', [QuestionnaireController::class, 'show'])->name('questionnaire.show');
        Route::post('/questionnaire/{step}', [QuestionnaireController::class, 'store'])->name('questionnaire.store');
    });

// Student routes
Route::middleware(['auth', 'role:student', 'consent', 'profile.complete'])
    ->prefix('student')->name('student.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');

        // Scholarship
        Route::get('/scholarships', [ScholarshipListingController::class, 'index'])->name('scholarships.index');

        // Matches
        Route::get('/matching', [MatchingResultsController::class, 'index'])->name('matching.index');
        Route::post('/matching/rerun', [StudentDashboard::class, 'rerunMatching'])->name('matching.rerun');

        // Notifications
        Route::get('/notifications', [StudentNotif::class, 'index'])->name('notifications.index');

        // Application
        Route::get('/my-applications', [StudentApplication::class, 'myApplications'])->name('applications.index');
        Route::patch('/applications/{application}/withdraw', [StudentApplication::class, 'withdraw'])->name('applications.withdraw');

        // Documents
        Route::get('/applications/{application}/documents', [DocumentUploadController::class, 'index'])->name('documents.index');
        Route::post('/applications/{application}/documents', [DocumentUploadController::class, 'store'])->name('documents.store');
        Route::delete('/documents/{document}', [DocumentUploadController::class, 'destroy'])->name('documents.destroy');
        Route::get('/documents/{document}/download', [DocumentUploadController::class, 'download'])->name('documents.download');
        Route::post('/scholarships/{scholarship}/apply', [StudentApplication::class, 'apply'])->name('scholarships.apply');

        // Profile
        Route::get('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'show'])->name('profile.show');
        Route::patch('/profile/personal', [\App\Http\Controllers\Student\ProfileController::class, 'updatePersonal'])->name('profile.personal');
        Route::patch('/profile/password', [\App\Http\Controllers\Student\ProfileController::class, 'updatePassword'])->name('profile.password');
    });

// Admin routes
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Scholarships
        Route::resource('scholarships', ScholarshipController::class);
        Route::patch('scholarships/{scholarship}/toggle', [ScholarshipController::class, 'toggleActive'])
            ->name('scholarships.toggle');
        Route::patch('scholarships/{scholarship}/criteria', [ScholarshipCriteriaController::class, 'update'])
            ->name('scholarships.criteria.update');

        // Students
        Route::get('students', [StudentController::class, 'index'])->name('students.index');
        Route::get('students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('students', [StudentController::class, 'store'])->name('students.store');
        Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show');
        Route::patch('students/{student}/toggle', [StudentController::class, 'toggleActive'])->name('students.toggle');

        // Applications
        Route::get('applications', [ApplicationController::class, 'index'])->name('applications.index');
        Route::get('applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
        Route::patch('applications/{application}/status', [ApplicationController::class, 'updateStatus'])->name('applications.status');
        Route::patch('applications/documents/{document}/verify', [ApplicationController::class, 'verifyDocument'])->name('documents.verify');

        // Notifications
        Route::get('notifications', [AdminNotif::class, 'index'])->name('notifications.index');
        Route::get('notifications/create', [AdminNotif::class, 'create'])->name('notifications.create');
        Route::post('notifications', [AdminNotif::class, 'store'])->name('notifications.store');
        Route::post('notifications/deadline-reminders', [AdminNotif::class, 'sendDeadlineReminders'])->name('notifications.deadlines');

        // Analytics
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('analytics/export/applications', [AnalyticsController::class, 'exportApplications'])->name('analytics.export.applications');
        Route::get('analytics/export/students', [AnalyticsController::class, 'exportStudents'])->name('analytics.export.students');

        // Document Downloads
        Route::get('documents/{document}/download', [DocumentUploadController::class, 'download'])->name('documents.admin.download');

        // Settings
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::patch('settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::patch('settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');

        // Audit Log
        Route::get('audit', [AuditLogController::class, 'index'])->name('audit.index');

        // Admin Profile
        Route::get('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile.show');
        Route::patch('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::patch('profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password');

        // Export All
        Route::get('export/all', [\App\Http\Controllers\Admin\AnalyticsController::class, 'exportAll'])->name('export.all');
    });
