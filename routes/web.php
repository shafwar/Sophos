<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

// Authentication Routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Registration Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Password Reset Routes
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ForgotPasswordController::class, 'reset'])->name('password.update');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Alert & Metrics Routes
    Route::get('/alerts/{category}', [DashboardController::class, 'getAlertsByCategory']);
    Route::get('/metrics', [DashboardController::class, 'getMetrics']);

    // Traffic Risk Routes
    Route::get('/traffic-risk/weekly', [DashboardController::class, 'getWeeklyTrafficRisk'])->name('traffic.weekly');
    Route::get('/traffic-risk/details/{week}/{level}', [DashboardController::class, 'getTrafficRiskDetails'])->name('traffic.details');
    Route::get('/traffic-risk/event/{id}', [DashboardController::class, 'getTrafficRiskEvent'])->name('traffic.event');
    Route::get('/alerts/low-risk', [DashboardController::class, 'getLowRiskData'])
    ->name('alerts.low-risk');

    // Main Navigation Routes
    Route::get('/overview', [DashboardController::class, 'overview'])->name('overview');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');

    // Profile Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.upload-photo');
        Route::delete('/photo', [ProfileController::class, 'deletePhoto'])->name('profile.delete-photo');
    });
});
