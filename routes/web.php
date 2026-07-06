<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InputDataController;

// ==========================
// LOGIN USER
// ==========================
Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
})->name('login.view');

Route::post('/login', [LoginController::class, 'login'])->name('login');

// ==========================
// LOGIN ADMIN
// ==========================
Route::get('/login-admin', [AdminAuthController::class, 'index'])->name('login.admin');
Route::post('/login-admin', [AdminAuthController::class, 'login'])->name('admin.login');
Route::get('/logout-admin', [AdminAuthController::class, 'logout'])->name('logout.admin');

// ==========================
// REGISTRASI
// ==========================
Route::get('/registrasi', function () {
    return view('registrasi');
});

Route::post('/registrasi', [RegisterController::class, 'store'])->name('register.store');

// ==========================
// DASHBOARD
// ==========================
Route::get('/dashboard', [DashboardController::class, 'userIndex'])->name('dashboard');
Route::get('/dashboard-admin', [DashboardController::class, 'adminIndex'])->name('admin.dashboard');

// ==========================
// INPUT DATA ADMIN
// ==========================
Route::get('/input-data', function () {
    return view('input-data');
})->name('input-data');

Route::post('/input-data', [InputDataController::class, 'store'])->name('input-data.store');

// ==========================
// UPDATE DATA
// ==========================
Route::get('/update-data', function () {
    return view('update-data');
});

Route::get('/rekap', function () {
    return view('rekap');
});

// ==========================
// KHS JASA
// ==========================
Route::get('/khs-jasa-2024', [DashboardController::class, 'khsJasa2024']);
Route::get('/khs-jasa-2025', [DashboardController::class, 'khsJasa2025']);
Route::get('/khs-jasa-2026', [DashboardController::class, 'khsJasa2026']);

// ==========================
// KHS PEMBERSIHAN
// ==========================
Route::get('/khs-pembersihan-2024', [DashboardController::class, 'pemb2024']);
Route::get('/khs-pembersihan-2025', [DashboardController::class, 'pemb2025']);
Route::get('/khs-pembersihan-2026', [DashboardController::class, 'pemb2026']);

// ==========================
// REGRESASI
// ==========================
Route::get('/regresasi-2025', [DashboardController::class, 'regresasi2025']);
Route::get('/regresasi-2026', [DashboardController::class, 'regresasi2026']);