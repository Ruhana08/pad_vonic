<?php

use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KelolaAdminController;
use App\Http\Controllers\Admin\NotifikasiController as AdminNotifikasiController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Admin\PollingAktifController;
use App\Http\Controllers\Admin\PollingArsipController;
use App\Http\Controllers\Admin\PollingController;
use App\Http\Controllers\Admin\ProfilController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Halaman utama diarahkan ke Login User (Sivitas DTEDI)
Route::get('/', function () {
    return view('auth.user-login');
})->name('login');

// Login user (Google SSO UGM)
Route::get('/user-login', function () {
    return view('auth.user-login');
})->name('user.login');
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// Logout bersama (mengarah ke login user atau admin sesuai role)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Alias /login -> /admin/login untuk admin
Route::redirect('/login', '/admin/login')->name('login.page');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Lupa & Reset Kata Sandi Admin
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Halaman User / Sivitas
Route::get('/home', function () {
    $semua = \App\Models\PeriodePolling::with(['paketWisata.galeri', 'desainKaos.galeri', 'partisipasi'])
        ->orderBy('tanggal_mulai', 'desc')
        ->get();

    $pollingAktif = $semua->firstWhere('status', 'aktif');
    $riwayatList  = $semua->filter(fn ($p) => $p->status === 'selesai' && (bool) ($p->is_public ?? true))
        ->take(3)
        ->values();

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

Route::get('/notifikasi', function () {
    return view('notifikasi');
})->name('notifikasi');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

    Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::redirect('/', '/admin/dashboard');

        Route::get('/polling/aktif', [PollingAktifController::class, 'index'])->name('polling.aktif');
        Route::get('/polling/detail/{id?}', [PollingAktifController::class, 'detail'])->name('polling.detail');
        Route::post('/polling/aktif/announce', [PollingAktifController::class, 'announce'])->name('polling.aktif.announce');
        Route::get('/polling/aktif/export-kaos', [PollingAktifController::class, 'exportKaos'])->name('polling.aktif.export-kaos');
        Route::get('/polling/arsip', [PollingArsipController::class, 'index'])->name('polling.arsip');
        Route::post('/polling/arsip/akses', [PollingArsipController::class, 'updateAccess'])->name('polling.arsip.access');
        Route::post('/polling/{id}/publish', [PollingArsipController::class, 'publish'])->name('polling.publish');
        Route::delete('/polling/{id}', [PollingArsipController::class, 'destroy'])->name('polling.destroy');
        Route::get('/polling/{id}', fn () => redirect()->route('admin.polling.arsip'))->whereNumber('id');
        Route::get('/polling/create', [PollingController::class, 'create'])->name('polling.create');
        Route::post('/polling', [PollingController::class, 'store'])->name('polling.store');
        Route::delete('/paket-wisata/{id}', [PollingController::class, 'destroyPaketWisata'])->name('paket-wisata.destroy');
        Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
        Route::post('/profil/update', [ProfilController::class, 'updateProfile'])->name('profil.update');
        Route::post('/profil/add-admin', [ProfilController::class, 'storeAdmin'])->name('profil.admin.store');

        Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
        Route::post('/akun/store', [AkunController::class, 'store'])->name('akun.store');
        Route::post('/akun/import-csv', [AkunController::class, 'importCsv'])->name('akun.import-csv');
        Route::delete('/akun/{id}', [AkunController::class, 'destroy'])->name('akun.destroy');

        // Khusus Master Admin: Kelola Akun Admin
        Route::middleware([\App\Http\Middleware\MasterAdminMiddleware::class])->group(function () {
            Route::get('/kelola-admin', [KelolaAdminController::class, 'index'])->name('kelola-admin.index');
            Route::post('/kelola-admin', [KelolaAdminController::class, 'store'])->name('kelola-admin.store');
            Route::put('/kelola-admin/{id}', [KelolaAdminController::class, 'update'])->name('kelola-admin.update');
            Route::delete('/kelola-admin/{id}', [KelolaAdminController::class, 'destroy'])->name('kelola-admin.destroy');
        });
        Route::get('/notifikasi', [AdminNotifikasiController::class, 'index'])->name('notifikasi');
        Route::get('/notifikasi/data', [AdminNotifikasiController::class, 'data'])->name('notifikasi.data');
        Route::post('/notifikasi/read-all', [AdminNotifikasiController::class, 'markReadAll'])->name('notifikasi.read-all');
        Route::post('/notifikasi/{id}/read', [AdminNotifikasiController::class, 'markRead'])->name('notifikasi.read');
        Route::view('/preview', 'admin.preview')->name('preview');
    });
});
