<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Login sebelum autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.process');
});

// Halaman setelah login
Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::get('/riwayat', function () {
        return view('riwayat');
    })->name('riwayat');

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');
});

if (file_exists(__DIR__.'/settings.php')) {
    require __DIR__.'/settings.php';
}