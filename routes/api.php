<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PollingController;
use App\Http\Controllers\Api\AdminPesertaController;
use App\Http\Controllers\Api\AdminPollingController;
use App\Http\Controllers\Api\AdminPaketWisataController;
use App\Http\Controllers\Api\AdminDesainKaosController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AdminGaleriPaketWisataController;
use App\Http\Controllers\Api\AdminGaleriDesainKaosController;
use App\Http\Controllers\Api\NotifikasiController;

// Login Admin menggunakan OTP
Route::post('/request-otp', [AuthController::class, 'requestOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

// Login User menggunakan email
Route::post('/login-user', [AuthController::class, 'loginUser']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

// Route publik
Route::get('/polling', [PollingController::class, 'index']);
Route::get('/polling/{id}/pilihan', [PollingController::class, 'pilihan']);
Route::get('/polling/{id}/hasil', [PollingController::class, 'hasil']);
Route::get('/polling/{id}', [PollingController::class, 'show']);

// Route yang membutuhkan autentikasi
Route::middleware('auth:sanctum')->group(function () {

    // User
    Route::post('/polling/{id}/vote', [PollingController::class, 'vote']);
    Route::get('/riwayat-voting', [PollingController::class, 'riwayat']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);

    // Notifikasi (User & Admin)
    Route::get('/notifikasi', [NotifikasiController::class, 'index']);
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'markReadAll']);
    Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'markRead']);

    // Admin
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        // Manajemen Peserta
        Route::get('/peserta', [AdminPesertaController::class, 'index']);
        Route::post('/peserta', [AdminPesertaController::class, 'store']);
        Route::post('/peserta/import', [AdminPesertaController::class, 'import']);
        Route::get('/peserta/{id}', [AdminPesertaController::class, 'show']);
        Route::put('/peserta/{id}', [AdminPesertaController::class, 'update']);
        Route::delete('/peserta/{id}', [AdminPesertaController::class, 'destroy']);

        // Manajemen Periode Polling
        Route::get('/polling', [AdminPollingController::class, 'index']);
        Route::post('/polling', [AdminPollingController::class, 'store']);
        Route::get('/polling/{id}', [AdminPollingController::class, 'show']);
        Route::put('/polling/{id}', [AdminPollingController::class, 'update']);
        Route::delete('/polling/{id}', [AdminPollingController::class, 'destroy']);
        Route::get('/polling/{id}/hasil', [AdminPollingController::class, 'hasil']);
        Route::post('/polling/{id}/publish', [AdminPollingController::class, 'publish']);
        Route::post('/polling/{id}/archive', [AdminPollingController::class, 'archive']);

        Route::post('/paket-wisata', [AdminPaketWisataController::class, 'store']);
        Route::post('/paket-wisata/{id}/update', [AdminPaketWisataController::class, 'update']);
        Route::put('/paket-wisata/{id}', [AdminPaketWisataController::class, 'update']);
        Route::delete('/paket-wisata/{id}', [AdminPaketWisataController::class, 'destroy']);

        // Manajemen Desain Kaos
        Route::post('/desain-kaos', [AdminDesainKaosController::class, 'store']);
        Route::post('/desain-kaos/{id}/update', [AdminDesainKaosController::class, 'update']);
        Route::put('/desain-kaos/{id}', [AdminDesainKaosController::class, 'update']);
        Route::delete('/desain-kaos/{id}', [AdminDesainKaosController::class, 'destroy']);
        Route::delete('/galeri-paket-wisata/{id}', [AdminGaleriPaketWisataController::class, 'destroy']);
        Route::delete('/galeri-desain-kaos/{id}', [AdminGaleriDesainKaosController::class, 'destroy']);
    });
});
