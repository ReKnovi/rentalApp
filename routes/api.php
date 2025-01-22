<?php

use App\Http\Controllers\AdminKycController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KycController;
use App\Http\Middleware\AuthenticateJWT;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

    //     // Registration routes
    // Route::post('/register', [AuthController::class, 'register']);
    // Route::post('/register/verify-otp', [AuthController::class, 'verifyRegisterOtp']);

    // // Login routes
    // Route::post('/login/send-otp', [AuthController::class, 'sendLoginOtp']);
    // Route::post('/login/verify-otp', [AuthController::class, 'verifyLoginOtp']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware(AuthenticateJWT::class);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::middleware('auth.jwt')->group(function () {
        Route::post('/kyc/upload', [KycController::class, 'upload']);
        Route::get('/kyc/status', [KycController::class, 'status']);
    });

    Route::middleware(['auth.jwt', 'role:admin'])->group(function () {
        Route::get('/admin/kyc/pending', [AdminKycController::class, 'getPending']);
        Route::post('/admin/kyc/approve/{kyc_id}', [AdminKycController::class, 'approve']);
        Route::post('/admin/kyc/reject/{kyc_id}', [AdminKycController   ::class, 'reject']);
    });
