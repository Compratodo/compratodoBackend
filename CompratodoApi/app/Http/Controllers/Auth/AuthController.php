<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\EmailVerification;
use App\Models\SmsVerification;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validar los datos recibidos
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|string',
            ],
            [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            ]
        );

        if($validator->fails()){
            return response()->json([
                'succes' => true,
                'message' => 'Error de validacion',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        // Buscar al usuario por email
        $user = User::where('email', $request->email)->first();

        // Verificar si el usuario existe y la contraseña es correcta
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

         if (is_null($user->email_verified_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Debes verificar tu correo antes de iniciar sesión.',
        ], 403);

        //validacion de 2FA
        if ($user->validation_2FA === 'email') {

            // Generar código alfanumérico
            $code = Str::upper(Str::random(6));

            // Eliminar códigos anteriores
            EmailVerification::where('user_id', $user->id)->delete();

            // Guardar el nuevo código
            EmailVerification::create([
                'user_id' => $user->id,
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);

            // Enviar por correo (esto lo programamos con Mailable más adelante)
            // Mail::to($user->email)->send(new EnviarCodigoMailable($code));

            return response()->json([
                'success' => true,
                'message' => 'Código de verificación enviado al correo electrónico.',
                'requires_2FA' => true,
                'method' => 'email'
            ]);
        }

        if ($user->validation_2FA === 'sms') {

            // Generar código numérico
            $code = mt_rand(100000, 999999);

            // Eliminar códigos anteriores
            SmsVerification::where('user_id', $user->id)->delete();

            // Guardar el nuevo código
            SmsVerification::create([
                'user_id' => $user->id,
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);

            // Enviar por SMS (esto lo programamos después)
            // SmsService::send($user->phone, "Tu código es: $code");

            return response()->json([
                'success' => true,
                'message' => 'Código de verificación enviado por SMS.',
                'requires_2FA' => true,
                'method' => 'sms'
            ]);
        }

        // Si no hay 2FA, generar el token normal   
    }
        // Revocar tokens anteriores si quieres (opcional)
        $user->tokens()->delete();

        // Crear token de Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        // Retornar respuesta con token
        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }



    public function logout(Request $request){

          // Revoca el token actual (cerrar sesión del usuario autenticado)
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'sesion cerrada correctamente'
        ]);
    }

    public function logoutFromAllDevices(Request $request) {

         // Revocar todos los tokens (cerrar sesión en todos los dispositivos)
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'sesion cerrada en todos los dispositivos'
        ]);
    }
    public function me(Request $request) {

        return response()->json([
            'user' => $request->user()
        ]);
}
}
