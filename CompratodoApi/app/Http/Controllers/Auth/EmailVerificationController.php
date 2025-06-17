<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EmailVerification;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    public function sendCode(Request $request)
        {
            return $this->sendVerificationCode($request->email);
        }

        public function sendVerificationCode($email)
        {
            $user = User::where('email', $email)->firstOrFail();

            EmailVerification::where('user_id', $user->id)->whereNull('verified_at')->delete();

            $code = strtoupper(Str::random(6));
            EmailVerification::create([
                'user_id' => $user->id,
                'code' => $code,
                'expires_at' => now()->addMinutes(10),
            ]);

            Mail::to($user->email)->send(new VerificationCodeMail([
                'userName' => $user->name,
                'verificationCode' => $code,
                'expiryTime' => '10 minutos',
                'welcomeMessage' => 'Verificación de correo electrónico',
                'mainMessage' => 'Tu código de verificación es:',
            ]));

            return response()->json(['success' => true, 'message' => 'Código enviado al correo']);
        }



    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        $verification = EmailVerification::where('user_id', $user->id)
            ->where('code', $request->code)
            ->whereNull('verified_at')
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$verification) {
            return response()->json([
                'success' => false, 
                'message' => 'Código inválido o expirado'
            ], 401);
        }

        $verification->update(['verified_at' => Carbon::now()]);
        $user->update(['email_verified_at' => Carbon::now()]);

        // ✅ Verificar si el campo ya está vacío antes de sobreescribir
        if (is_null($user->email_verified_at)) {
            $user->email_verified_at = now();
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Correo verificado correctamente'
        ]);
    }
}

