<?php

use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PollingAktifController;
use App\Http\Controllers\Admin\PollingArsipController;
use App\Http\Controllers\Admin\PollingController;
use App\Http\Controllers\Admin\ProfilController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Halaman pertama yang dibuka admin: Login / Register
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.page');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::redirect('/', '/admin/dashboard');

    Route::get('/polling/aktif', [PollingAktifController::class, 'index'])->name('polling.aktif');
    Route::post('/polling/aktif/announce', [PollingAktifController::class, 'announce'])->name('polling.aktif.announce');
    Route::get('/polling/aktif/export-kaos', [PollingAktifController::class, 'exportKaos'])->name('polling.aktif.export-kaos');
    Route::get('/polling/arsip', [PollingArsipController::class, 'index'])->name('polling.arsip');
    Route::get('/polling/create', [PollingController::class, 'create'])->name('polling.create');
    Route::post('/polling', [PollingController::class, 'store'])->name('polling.store');
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::post('/profil/update', [ProfilController::class, 'updateProfile'])->name('profil.update');
    Route::post('/profil/add-admin', [ProfilController::class, 'storeAdmin'])->name('profil.admin.store');

    Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
    Route::post('/akun/store', [AkunController::class, 'store'])->name('akun.store');
    Route::post('/akun/import-csv', [AkunController::class, 'importCsv'])->name('akun.import-csv');
    Route::delete('/akun/{id}', [AkunController::class, 'destroy'])->name('akun.destroy');
    Route::view('/preview', 'admin.preview')->name('preview');
});