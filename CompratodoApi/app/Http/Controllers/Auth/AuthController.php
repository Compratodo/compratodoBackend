<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validar los datos recibidos
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'succes' => true,
                'message' => 'error de validacion',
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
