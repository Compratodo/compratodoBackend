<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EmailVerification;
use App\Models\SmsVerification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\Mail;
use App\Services\TwilioService;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VerificationController extends Controller
{
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
            'method' => 'required|in:email,sms',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
        }

        if ($request->method === 'email') {
            $verification = EmailVerification::where('user_id', $user->id)
                ->where('code', $request->code)
                ->whereNull('verified_at')
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if (!$verification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Código inválido o expirado',
                ], 401);
            }

            $verification->verified_at = Carbon::now();
            $verification->save();

            $user->email_verified_at = Carbon::now();
            $user->save();


        } elseif ($request->method === 'sms') {
            $verification = SmsVerification::where('user_id', $user->id)
                ->where('code', $request->code)
                ->whereNull('verified_at')
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if (!$verification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Código inválido o expirado',
                ], 401);
            }

            $verification->verified_at = Carbon::now();
            $verification->save();

             //  Actualiza el método de validación
            $user->update([
                'validation_2FA' => 'sms',
            ]);
        }

        //  Emitir token de acceso
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Código verificado correctamente',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'method' => 'required|in:email,sms',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
        }

        // Eliminar códigos anteriores no verificados
        if ($request->method === 'email') {
            EmailVerification::where('user_id', $user->id)->whereNull('verified_at')->delete();

            $code = strtoupper(Str::random(6)); // Código alfanumérico
            EmailVerification::create([
                'user_id' => $user->id,
                'code' => $code,
                'expires_at' => now()->addMinutes(10),
            ]);

            $data = [
                'userName' => $user->name,
                'verificationCode' => $code,
                'expiryTime' => '10 minutos',
                'welcomeMessage' => 'Estás a un paso de verificar tu cuenta.',
                'mainMessage' => 'Ingresa el siguiente código para completar el proceso de verificación:',
                'actionUrl' => null,
                'actionText' => null,
                'additionalContent' => null,
            ];

            Mail::to($user->email)->send(new VerificationCodeMail($data));

        } elseif ($request->method === 'sms') {
            if (!$user->phone) {
                return response()->json(['success' => false, 'message' => 'El usuario no tiene teléfono registrado'], 400);
            }

            SmsVerification::where('user_id', $user->id)->whereNull('verified_at')->delete();

            $code = rand(100000, 999999); // Solo números
            SmsVerification::create([
                'user_id' => $user->id,
                'phone' => $user->phone,
                'code' => $code,
                'expires_at' => now()->addMinutes(5),
            ]);

            $phone = $user->phone;

            // Si empieza con "3" y tiene 10 dígitos (formato colombiano típico)
            if (preg_match('/^3\d{9}$/', $phone)) {
                $phone = '+57' . $phone;
}

            $twilio = new TwilioService();
            $twilio->sendSms($phone, "Tu código de verificación es: $code");

            return response()->json([
                'success' => true,
                'message' => 'Código enviado por SMS correctamente',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Código enviado correctamente'
        ]);
    }

}
