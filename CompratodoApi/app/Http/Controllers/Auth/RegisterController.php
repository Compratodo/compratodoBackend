<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller{

        public function register(Request $request)
        {
            // Validar los datos
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'accepted_terms' => 'required|accepted',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'error de validacion',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Crear el usuario
            $user = User::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'accepted_terms' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado correctamente',
                'user' => $user
            ], 201);
        }
}