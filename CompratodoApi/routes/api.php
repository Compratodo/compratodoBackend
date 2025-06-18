<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\SellerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;


Route::prefix('auth')->group(function () {
    // Registro
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);


    //recuperacion de contrasena por email
    Route::post('/forgot-password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']); 
    //ruta para validar token
    Route::get('/reset-password/validate', [ForgotPasswordController::class, 'validateToken']);
    //Ruta para guardar nueva contrasena
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
    

    //recuperacion de contrasena por sms (solo si el user tiene id-cedula registrada)
    Route::post('/forgot-password/sms', [ForgotPasswordController::class, 'sendCodeBySms']);
    Route::post('/reset-password/sms', [ForgotPasswordController::class, 'resetPasswordBySms']);


    //recuperacion de contrasena por pregunta de seguridad (solo si el user tiene id-cedula registrada)
    Route::post('/forgot-password/security-question', [ForgotPasswordController::class, 'getSecurityQuestion']);
    Route::post('/reset-password/security-question', [ForgotPasswordController::class, 'resetPasswordBySecurityQuestion']);


    // Verificación de código de 2FA (SMS, App, etc.) para login
    Route::post('/2fa/verify-code', [VerificationController::class, 'verifyCode']);
    Route::post('/2fa/send-code', [VerificationController::class, 'sendCode']);


    // Verificación de email en register
    Route::prefix('verify/email')->controller(EmailVerificationController::class)->group(function () {
        Route::post('/send', 'sendCode');
        Route::post('/resendCode', 'resendCode');
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



