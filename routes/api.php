<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PollingController;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::get('/polling', [PollingController::class, 'index']);

Route::get('/polling/{id}/pilihan', [PollingController::class, 'pilihan']);
Route::get('/polling/{id}/hasil', [PollingController::class, 'hasil']);
Route::get('/polling/{id}', [PollingController::class, 'show']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/polling/{id}/vote', [PollingController::class, 'vote']);

    Route::get('/riwayat-voting', [PollingController::class, 'riwayat']);

    Route::get('/polling/{id}/hasil', [PollingController::class, 'hasil']);

    Route::get('/profile', [AuthController::class, 'profile']);

});