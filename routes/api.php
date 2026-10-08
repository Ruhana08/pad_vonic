<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PollingController;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::get('/polling', [PollingController::class, 'index']);
Route::get('/polling/{id}', [PollingController::class, 'show']);
Route::get('/polling/{id}/pilihan', [PollingController::class, 'pilihan']);

Route::post('/polling/{id}/vote', [PollingController::class, 'vote'])
    ->middleware('auth:sanctum');

Route::get('/riwayat', [PollingController::class, 'riwayat'])
    ->middleware('auth:sanctum');
