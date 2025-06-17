<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\SellerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailVerificationController;




Route::prefix('auth')->group(function () {
    // Registro
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);


    // Verificación de código de 2FA (SMS, App, etc.) para login
    Route::post('/2fa/verify-code', [VerificationController::class, 'verifyCode']);
    Route::post('/2fa/send-code', [VerificationController::class, 'sendCode']);

    // Verificación de email en register
    Route::prefix('verify/email')->controller(EmailVerificationController::class)->group(function () {
        Route::post('/send', 'sendCode');
        Route::post('/check', 'verifyCode');
    });

    // Rutas protegidas
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logoutFromAllDevices', [AuthController::class, 'logoutFromAllDevices']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/seller/register', [SellerController::class, 'register']);
        Route::get('/seller/profile', [SellerController::class, 'profile']);
        Route::post('/seller/update', [SellerController::class, 'update']);
    });
});



