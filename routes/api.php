<?php

use App\Http\Controllers\Api\AlatController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\PelangganController;
use App\Http\Controllers\Api\PelangganDataController;
use App\Http\Controllers\Api\PenyewaanController;
use App\Http\Controllers\Api\PenyewaanDetailController;
use Illuminate\Support\Facades\Route;

// Publik, tidak butuh token
Route::post('/login', [AuthController::class, 'login']);

// Wajib token: header Authorization: Bearer <token>
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('kategori', KategoriController::class)->except(['create', 'edit']);
    Route::apiResource('alat', AlatController::class)->except(['create', 'edit']);
    Route::apiResource('pelanggan', PelangganController::class)->except(['create', 'edit']);
    Route::apiResource('pelanggan-data', PelangganDataController::class)->except(['create', 'edit']);
    Route::apiResource('penyewaan', PenyewaanController::class)->except(['create', 'edit']);
    Route::apiResource('penyewaan-detail', PenyewaanDetailController::class)->except(['create', 'edit']);
});