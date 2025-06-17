<?php

namespace App\Http\Controllers\Auth;

use App\Models\TwoFactorCode;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use App\Services\TwilioService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class VerificationController extends Controller {

    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'method' => 'required|in:email,sms',
        ]);
    
        $user = User::where('email', $request->email)->firstOrFail();
    
        // Limpia códigos antiguos no verificados
        TwoFactorCode::where('user_id', $user->id)->whereNull('verified_at')->delete();
    
        // Genera el código
        $code = $request->method === 'email'
            ? strtoupper(Str::random(6))
            : rand(100000, 999999);
    
        TwoFactorCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'method' => $request->method,
            'expires_at' => now()->addMinutes(10),
        ]);
    
        if ($request->method === 'email') {
            Mail::to($user->email)->send(new VerificationCodeMail([
                'userName' => $user->name,
                'verificationCode' => $code,
                'expiryTime' => '10 minutos',
                'welcomeMessage' => 'Verificación 2FA',
                'mainMessage' => 'Tu código 2FA es:',
            ]));
    
        } else {
            $phone = $user->phone;
            if (preg_match('/^3\d{9}$/', $phone)) {
                $phone = '+57' . $phone;
            }
    
            $twilio = new TwilioService();
            $twilio->sendSms($phone, "Tu código 2FA es: $code");
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Código enviado correctamente'
        ]);
    }
    
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
            'method' => 'required|in:email,sms',
        ]);
    
        $user = User::where('email', $request->email)->firstOrFail();
    
        $verification = TwoFactorCode::where('user_id', $user->id)
            ->where('code', $request->code)
            ->where('method', $request->method)
            ->whereNull('verified_at')
            ->where('expires_at', '>', Carbon::now())
            ->first();
    
        if (!$verification) {
            return response()->json([
                'success' => false,
                'message' => 'Código inválido o expirado',
            ], 401);
        }
    
        $verification->update(['verified_at' => now()]);
    
        // Marca que el usuario tiene 2FA activado
        $user->update(['validation_2FA' => $request->method]);
    
        // Opcional: emitir token si es parte del flujo de login
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'success' => true,
            'message' => 'Código verificado correctamente',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }
}


