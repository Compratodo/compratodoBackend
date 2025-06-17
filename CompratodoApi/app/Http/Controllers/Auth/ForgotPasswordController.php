<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\PasswordReset;
use App\Mail\ResetPasswordLinkMail;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\returnSelf;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request) {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un usuario con ese correo electrónico'
            ], 404);
        }

        //eliminar tokens anteriores no usados 
        PasswordReset::where('user_id', $user->id)
            ->whereNull('used_at')
            ->delete();

        //crear token y guardar
        $token = Str::random(64);

        PasswordReset::create([
            'user_id' => $user->id,
            'token' => $token,
            'method' => 'email',
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);


        //generar URL de recuperacion 
        $url = url('/reset-password?token=' . $token . '&email=' . urlencode($user->email));


        //enviar correo
        Mail::to($user->email)->send(new ResetPasswordLinkMail($user->name, $url));

        return response()->json([
            'success' => true,
            'message' => 'Se ha enviado el enlace de recuperacion al correo'
        ]);
    }

    public function validateToken(Request $request) {

        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $reset = \App\Models\PasswordReset::where('user_id', $user->id)
            ->where('token', $request->token)
            ->where('method', 'email')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if(!$reset) {
            return response()->json([
                'success' => true,
                'message' => 'Token inválido o expirado'
            ], 400);
        } 

        return response()->json([
            'success' => true,
            'message' => 'Token válido. Puedes Continuar con el cambio de contraseña'
        ]);
    }

    public function resetPassword(Request $request) {

        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $reset = \App\Models\PasswordReset::where('user_id', $user->id)
            ->where('token', $request->token)
            ->where('method', 'email')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if(!$reset) {
            return response()->json([
                'success' => false,
                'message' => 'token invalido o expirado'
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $reset->update(['used_at' =>now()]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente'
        ]);
    }
}
