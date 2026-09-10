<?php

use App\Http\Controllers\Api\AlatController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\PelangganAuthController;
use App\Http\Controllers\Api\PelangganController;
use App\Http\Controllers\Api\PelangganDataController;
use App\Http\Controllers\Api\PenyewaanController;
use App\Http\Controllers\Api\PenyewaanDetailController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

// ============ PELANGGAN ENDPOINTS (auth:pelanggan-api) ============
// Must be BEFORE admin routes to avoid route shadowing
Route::post('/pelanggan/register', [PelangganAuthController::class, 'register']);
Route::post('/pelanggan/login', [PelangganAuthController::class, 'login']);

Route::middleware('auth:pelanggan-api')->group(function () {
    Route::post('/pelanggan/logout', [PelangganAuthController::class, 'logout']);
    Route::get('/pelanggan/me', [PelangganAuthController::class, 'me']);
    Route::patch('/pelanggan/profile', [PelangganAuthController::class, 'updateProfile']);
    
    Route::get('/pelanggan/alat', [AlatController::class, 'index']);
    Route::get('/pelanggan/alat/{id}', [AlatController::class, 'show']);
    Route::get('/pelanggan/kategori', [KategoriController::class, 'index']);
    Route::get('/pelanggan/kategori/{id}', [KategoriController::class, 'show']);
    
    Route::post('/pelanggan/penyewaan', [PenyewaanController::class, 'store']);
    Route::get('/pelanggan/penyewaan', [PenyewaanController::class, 'index']);
    Route::get('/pelanggan/penyewaan/{id}', [PenyewaanController::class, 'show']);
    Route::patch('/pelanggan/penyewaan/{id}', [PenyewaanController::class, 'update']);
    Route::delete('/pelanggan/penyewaan/{id}', [PenyewaanController::class, 'destroy']);
    
    Route::post('/pelanggan/penyewaan-detail', [PenyewaanDetailController::class, 'store']);
    Route::get('/pelanggan/penyewaan-detail', [PenyewaanDetailController::class, 'index']);
    Route::get('/pelanggan/penyewaan-detail/{id}', [PenyewaanDetailController::class, 'show']);
    Route::patch('/pelanggan/penyewaan-detail/{id}', [PenyewaanDetailController::class, 'update']);
    Route::delete('/pelanggan/penyewaan-detail/{id}', [PenyewaanDetailController::class, 'destroy']);
    
    Route::post('/pelanggan/data', [PelangganDataController::class, 'store']);
    Route::get('/pelanggan/data', [PelangganDataController::class, 'index']);
    Route::get('/pelanggan/data/{id}', [PelangganDataController::class, 'show']);
    Route::patch('/pelanggan/data/{id}', [PelangganDataController::class, 'update']);
    Route::delete('/pelanggan/data/{id}', [PelangganDataController::class, 'destroy']);
});

// ============ ADMIN ENDPOINTS (auth:api) ============
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('kategori', KategoriController::class)->except(['create', 'edit']);
    Route::apiResource('alat', AlatController::class)->except(['create', 'edit']);
    Route::apiResource('pelanggan', PelangganController::class)->except(['create', 'edit']);
    Route::apiResource('pelanggan-data', PelangganDataController::class)->except(['create', 'edit']);
    
    Route::post('/penyewaan', [PenyewaanController::class, 'store']);
    Route::get('/penyewaan', [PenyewaanController::class, 'index']);
    Route::get('/penyewaan/{id}', [PenyewaanController::class, 'show']);
    Route::patch('/penyewaan/{id}', [PenyewaanController::class, 'update']);
    Route::delete('/penyewaan/{id}', [PenyewaanController::class, 'destroy']);
    
    Route::post('/penyewaan-detail', [PenyewaanDetailController::class, 'store']);
    Route::get('/penyewaan-detail', [PenyewaanDetailController::class, 'index']);
    Route::get('/penyewaan-detail/{id}', [PenyewaanDetailController::class, 'show']);
    Route::patch('/penyewaan-detail/{id}', [PenyewaanDetailController::class, 'update']);
    Route::delete('/penyewaan-detail/{id}', [PenyewaanDetailController::class, 'destroy']);
});