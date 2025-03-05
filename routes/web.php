<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\ForgotPasswordController;

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
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');

    // Alerts Routes
    Route::prefix('alerts')->group(function () {
        Route::get('/{category}', [DashboardController::class, 'getAlertsByCategory']);
        Route::get('/low-risk', [DashboardController::class, 'getLowRiskData'])->name('alerts.low-risk');

        // Export Routes
        Route::get('/{category}/pdf', [DashboardController::class, 'exportAlertsPDF'])->name('alerts.export.pdf');
        Route::get('/{category}/csv', [DashboardController::class, 'exportAlertsCSV'])->name('alerts.export.csv');
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
});
