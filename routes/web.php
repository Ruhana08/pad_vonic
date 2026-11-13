<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

<<<<<<< HEAD
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return view('welcome');
});
=======

Route::get("/", function () {
    return view("home");
}); 
Route::get("/riwayat", function () {    
    return view("riwayat");
});

require __DIR__.'/settings.php';
>>>>>>> 8f64aae (add features)
