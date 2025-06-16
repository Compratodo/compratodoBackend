<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\SellerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('auth')->group(function () {
    //registro
    Route::post('/register', [RegisterController::class, 'register']);

    //login
    Route::post('/login', [AuthController::class, 'login']);
    // verificar codigo
    Route::post('/verify-code', [VerificationController::class, 'verifyCode']);
     // reenviar código 
    Route::post('/send-code', [VerificationController::class, 'sendCode']);


    //rutas protegidas (requieren autenticacion con sanctum )
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logoutFromAllDevices', [AuthController::class, 'logoutFromAllDevices']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/seller/register', [SellerController::class, 'register']);
        Route::get('/seller/profile', [SellerController::class, 'profile']);
        Route::post('/seller/update', [SellerController::class, 'update']);
    });
});
