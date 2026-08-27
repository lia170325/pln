<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InputDataController;
use App\Http\Controllers\Admin\UploadController;

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

Route::get('/logout-admin', [AdminAuthController::class, 'logout'])->name('logout.admin');

// TAMBAHAN: Route Logout User (sebelumnya belum terdaftar sehingga
// tombol Logout di Dashboard User mengarah ke halaman yang tidak ada).
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

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

Route::post('/input-data', [InputDataController::class, 'storeKhs'])->name('input-data.store');

// ==========================
// UPLOAD EXCEL
// ==========================
Route::get('/upload-excel', [UploadController::class, 'index'])->name('upload.index');

Route::post('/upload-excel',
    [InputDataController::class,'storeKhs'])
    ->name('upload.store');

// ==========================
// TAMBAHAN: Upload terpisah - Monitoring Tiang & Monitoring Pelanggan
// ==========================
Route::post('/input-data/tiang', [InputDataController::class, 'storeTiang'])
    ->name('input-data.store.tiang');

Route::post('/input-data/pelanggan', [InputDataController::class, 'storePelanggan'])
    ->name('input-data.store.pelanggan');

// ==========================
// UPDATE DATA
// ==========================
Route::get('/update-data', function () {
    return view('update-data');
});

Route::get('/rekap', function () {
    return view('rekap');
});

Route::get('/sheet/{id}', [DashboardController::class, 'show'])
    ->name('sheet.show');

// ==========================
// TAMBAHAN: Live Search (Find/Search) Daftar Sheet
// ==========================
Route::get('/search-sheet', [DashboardController::class, 'searchSheet'])
    ->name('search.sheet');