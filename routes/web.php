<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\OverviewController;
use App\Http\Controllers\RiskController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Register Routes
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Password Reset Routes
    Route::prefix('password')->name('password.')->group(function () {
        Route::get('/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('request');
        Route::post('/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('email');
        Route::get('/reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('reset');
        Route::post('/reset', [ForgotPasswordController::class, 'reset'])->name('update');
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard & Main Navigation
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/overview', [DashboardController::class, 'overview'])->name('overview');

Route::get('/debug-api', function() {
    $sophosApi = app(\App\Services\SophosApiService::class);

    echo "<h2>API Authentication Test</h2>";
    echo "<pre>";

    // Test Authentication
    $reflection = new ReflectionMethod($sophosApi, 'authenticate');
    $reflection->setAccessible(true);
    $authResult = $reflection->invoke($sophosApi);
    var_dump("Authentication result:", $authResult);

    // Test API Call
    echo "<h2>Endpoints Raw Data</h2>";
    $reflection = new ReflectionMethod($sophosApi, 'makeApiRequest');
    $reflection->setAccessible(true);
    $endpoints = $reflection->invoke($sophosApi, '/endpoint/v1/endpoints');
    var_dump("API Response Count:", $endpoints ? count($endpoints['items'] ?? []) : 0);

    // Test getUsers method
    echo "<h2>GetUsers Result</h2>";
    $users = $sophosApi->getUsers();
    var_dump("Users Stats:", $users ?? "No data");

    return "Check the output above";
});
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');

    // Alerts Routes
    Route::prefix('alerts')->group(function () {
        Route::get('/{category}', [DashboardController::class, 'getAlertsByCategory']);
        Route::get('/low-risk', [DashboardController::class, 'getLowRiskData'])->name('alerts.low-risk');

        // Export Routes (DISABLED, gunakan /risk/export/{category}/{format} di bawah)
        // Route::get('/{category}/pdf', [DashboardController::class, 'exportAlertsPDF'])->name('alerts.export.pdf');
        // Route::get('/{category}/csv', [DashboardController::class, 'exportAlertsCSV'])->name('alerts.export.csv');
    });

    // Traffic Risk Routes
    Route::prefix('traffic-risk')->name('traffic.')->group(function () {
        Route::get('/weekly', [DashboardController::class, 'getWeeklyTrafficRisk'])->name('weekly');
        Route::get('/details/{month}/{level}', [DashboardController::class, 'getTrafficRiskDetails'])->name('details');
        Route::get('/event/{id}', [DashboardController::class, 'getTrafficRiskEvent'])->name('event');
        Route::get('/monthly-details/{month}', [DashboardController::class, 'getMonthlyDetails'])->name('monthly-details');
    });

    // Metrics Route
    Route::get('/metrics', [DashboardController::class, 'getMetrics']);

    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::post('/photo', [ProfileController::class, 'uploadPhoto'])->name('upload-photo');
        Route::delete('/photo', [ProfileController::class, 'deletePhoto'])->name('delete-photo');
    });

    // AJAX Routes
    Route::post('/check-email', [AuthController::class, 'checkEmail'])->name('check.email');

    // Risk Routes
    Route::get('/dashboard-risk', [RiskController::class, 'dashboard'])->name('risk.dashboard');
    Route::get('/risk/export/{category}/{format}', [RiskController::class, 'export'])->name('risk.export');

    // Activity Log (khusus admin, pengecekan di controller)
    Route::get('/admin/activity-log', [DashboardController::class, 'activityLog'])->name('admin.activity-log');

    // Admin: Pending user approval
    Route::get('/admin/pending-users', [DashboardController::class, 'pendingUsers'])->name('admin.pending-users');
    Route::post('/admin/approve-user/{id}', [DashboardController::class, 'approveUser'])->name('admin.approve-user');
    Route::post('/admin/decline-user/{id}', [DashboardController::class, 'declineUser'])->name('admin.decline-user');

    // Admin: User List
    Route::get('/admin/user-list', [DashboardController::class, 'userList'])->name('admin.user-list');
    Route::delete('/admin/delete-user/{id}', [DashboardController::class, 'deleteUser'])->name('admin.delete-user');

    // Export activity log user
    Route::get('/activity-log/export', [DashboardController::class, 'exportUserLog'])->name('activity-log.export');

    // History Data untuk user
    Route::get('/history', [DashboardController::class, 'historyData'])->name('history.data');

    // Export history data user
    Route::get('/history/export', [DashboardController::class, 'exportHistoryData'])->name('history.export');

    // New route for getting user alerts
    Route::get('/dashboard/user-alerts', [DashboardController::class, 'getUserAlerts'])->name('dashboard.user-alerts');

    // New route for getting user alerts by category
    Route::get('/user-alerts/{category}', [DashboardController::class, 'getUserAlertsByCategory'])->name('user-alerts.by-category');
});
