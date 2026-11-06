<?php

use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PollingAktifController;
use App\Http\Controllers\Admin\PollingArsipController;
use App\Http\Controllers\Admin\PollingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProfilController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::redirect('/', '/admin/dashboard');

    Route::get('/polling/aktif', [PollingAktifController::class, 'index'])->name('polling.aktif');
    Route::get('/polling/arsip', [PollingArsipController::class, 'index'])->name('polling.arsip');
    Route::get('/polling/create', [PollingController::class, 'create'])->name('polling.create');
    Route::post('/polling', [PollingController::class, 'store'])->name('polling.store');
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');

    Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
    Route::view('/preview', 'admin.preview')->name('preview');
});