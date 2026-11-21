<?php

use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PollingAktifController;
use App\Http\Controllers\Admin\PollingArsipController;
use App\Http\Controllers\Admin\PollingController;
use App\Http\Controllers\Admin\ProfilController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Models\PeriodePolling;
use Illuminate\Support\Facades\Route;

// Auth User & Admin
Route::get('/', function () {
    return view('auth.user-login');
})->name('login');

Route::get('/login', function () {
    return view('auth.user-login');
})->name('user.login');

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login.page');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/login/process', [LoginController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp');
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');
// Google OAuth — Sivitas Login
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// Halaman User / Sivitas
Route::get('/home', function () {
    $semua = \App\Models\PeriodePolling::with(['paketWisata', 'desainKaos', 'partisipasi'])
        ->withCount('partisipasi')
        ->orderBy('tanggal_mulai', 'desc')
        ->get();

    $pollingAktif = $semua->firstWhere('status', 'aktif');
    if ($pollingAktif) {
        $pollingAktif->partisipan_count = $pollingAktif->partisipasi_count;
    }
    $riwayatList  = $semua->where('status', 'selesai')->take(3)->values();

    return view('home', compact('pollingAktif', 'riwayatList'));
})->name('home');

Route::get('/riwayat', function () {
    return view('riwayat');
})->name('riwayat');

Route::get('/voting', function () {
    return view('voting');
})->name('voting');

Route::get('/profile', function () {
    return view('profile');
})->name('profile');

// Area Admin Vonic
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::redirect('/', '/admin/dashboard');

    Route::get('/polling/aktif', [PollingAktifController::class, 'index'])->name('polling.aktif');
    Route::get('/polling/detail/{id?}', [PollingAktifController::class, 'detail'])->name('polling.detail');
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

if (file_exists(__DIR__.'/settings.php')) {
    require __DIR__.'/settings.php';
}
