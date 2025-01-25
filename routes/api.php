<?php

use App\Http\Controllers\AdminKycController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\LandlordController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TenantController;
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

    Route::group(['middleware' => ['auth.jwt', 'role:landlord']], function () {
        Route::post('/landlord/store-rooms', [LandlordController::class, 'store']); // Add a room
        Route::get('/landlord/rooms', [LandlordController::class, 'index']); // View landlord's rooms
        Route::post('/landlord/update-rooms/{id}', [LandlordController::class, 'update']); // Edit a room
        Route::delete('/landlord/rooms/{id}', [LandlordController::class, 'destroy']); // Delete a room
        Route::post('/rooms/{id}/highlight', [RoomController::class, 'highlightRoom']);
        Route::put('/rooms/{id}/rules', [RoomController::class, 'updateRentalRules']);//update rental rules

    });


    Route::middleware(['auth.jwt', 'role:tenant'])->group(function () {
        // Tenant portal routes
        Route::get('/tenant/rooms', [TenantController::class, 'browseRooms']);
        Route::post('/tenant/apply', [TenantController::class, 'applyForRoom']);
        Route::get('/tenant/applications', [TenantController::class, 'getApplications']);
        Route::get('/tenant/saved-rooms', [TenantController::class, 'getSavedRooms']);
        Route::post('/tenant/save-room', [TenantController::class, 'saveRoom']);
        Route::delete('/tenant/remove-room', [TenantController::class, 'removeSavedRoom']);
    });

    Route::middleware(['auth.jwt'])->group(function () {
        Route::post('/contracts', [ContractController::class, 'createContract']);
        Route::post('/contracts/{id}/sign/tenant', [ContractController::class, 'signContractAsTenant'])->middleware('role:tenant');
        Route::post('/contracts/{id}/sign/landlord', [ContractController::class, 'signContractAsLandlord'])->middleware('role:landlord');
        Route::get('/contracts/{id}', [ContractController::class, 'show']);
    });



