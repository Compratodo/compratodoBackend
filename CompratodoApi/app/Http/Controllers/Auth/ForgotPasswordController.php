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
use App\Services\TwilioService;

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

        $reset = PasswordReset::where('user_id', $user->id)
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

    /////////////////////////////////////////////////////////////////////////
    
    
    //  RECUPERACION POR SMS
    public function sendCodeBySms(Request $request) {

        $request->validate([
            'id_number' => 'required|string',
        ]);

        $user = User::where('id_number', $request->id_number)->first();

        if(!$user || !$user->phone) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un usuario con esa cédula o sin número de telefono registrado'
            ], 404);
        }

        //generar codigo numerico
        $code = rand(100000, 999999);
        
        //eliminar codigos anteriorres
        PasswordReset::where('user_id', $user->id)
            ->where('method', 'sms')
            ->delete();

        //guardar codigo
        PasswordReset::create([
            'user_id' => $user->id,
            'code' => $code,
            'method' => 'sms',
            'expires_at' => now()->addMinutes(10),
        ]);
        
        // Enviar Sms
        $phone = preg_match('/^3\d{9}$/', $user->phone) ? '+57' . $user->phone : $user->phone;

        $twilio = new TwilioService();
        $twilio->sendSms($phone, "Tu código para recuperar tu contraseña es: $code");

        return response()->json([
            'success' => true,
            'message' => 'codigo enviado por SMS',
        ]);
    }

    public function resetPasswordBySms(Request $request) {

        $request->validate([
            'id_number' => 'required|string',
            'code' => 'required|string',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = User::where('id_number', $request->id_number)->first();

        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'usuario no encontrado',
            ], 404);
        }

        $reset = PasswordReset::where('user_id', $user->id)
            ->where('code', $request->code)
            ->where('method', 'sms')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if(!$reset) {
            return response()->json([
                'success' =>false,
                'message' =>'Código inválido o expirado'
            ], 400);
        }
        
        $user->password = Hash::make($request->password);
        $user->save();


        $reset->update(['used_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente'
        ]);
    }

    /////////////////////////////////////////////////////////////////////////

    // RECUPERACION CON PREGUNTA DE SEGURIDAD
    public function getSecurityQuestion(Request $request) {
        $request->validate([
            'id_number' => 'required|string',
        ]);

        $user = User::where('id_number', $request->id_number)->first();

        if(!$user || !$user->securityQuestion) {
            return response()->json([
                'succcess' => false,
                'message' => 'No se encontró una pregunta de seguridad para este usuario'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'question' => $user->securityQuestion->question,
        ]);
    }

    //metodo resetPasswordBySecurityQuestion
    public function resetPasswordBySecurityQuestion(Request $request) {

        $request->validate([
            'id_number' => 'required|string',
            'answer' =>'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('id_number', $request->id_number)->first();

        if(!$user ||  !$user->securityQuestion) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró información de seguridad para este usuario',
            ], 404);
        }

        //comparacion segura
        if(strtolower(trim($user->securityQuestion->answer)) !== strtolower(trim($request->answer))) {
            return response()->json([
                'success' => false,
                'message' => 'Respuesta incorrecta',
            ], 403);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente'
        ]);
    }
}
