<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\DashboardController; // Menambahkan controller dashboard

// Rute untuk halaman login (diakses langsung tanpa /login)
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login'); // Menampilkan form login langsung di root

// Rute untuk login
Route::post('/login', [AuthController::class, 'login'])->name('login.submit'); // Memproses login

// Rute untuk logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); // Memproses logout

// Rute untuk reset password
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ForgotPasswordController::class, 'reset'])->name('password.update');

// Rute untuk Halaman Dashboard (akses hanya setelah login)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); // Menggunakan controller DashboardController
});


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/alerts/{category}', [DashboardController::class, 'getAlertsByCategory']);
    Route::get('/metrics', [DashboardController::class, 'getMetrics']);
    
});