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
                'phone' => 'nullable|string|size:10|unique:users',
                'id_number' => 'nullable|string|max:20|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'accepted_terms' => 'required|accepted',
            ],
            [
                'name.required' => 'El nombre es obligatorio.',
                'name.string' => 'El nombre debe ser una cadena de texto.',
                'name.max' => 'El nombre no puede tener más de 255 caracteres.',

                'last_name.string' => 'El apellido debe ser una cadena de texto.',
                'last_name.max' => 'El apellido no puede tener más de 255 caracteres.',

                'email.required' => 'El correo electrónico es obligatorio.',
                'email.string' => 'El correo electrónico debe ser una cadena de texto.',
                'email.email' => 'El correo electrónico debe tener un formato válido.',
                'email.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
                'email.unique' => 'Este correo ya está registrado.',

                'phone.string' => 'El número de teléfono debe ser una cadena.',
                'phone.size' => 'El número de teléfono debe tener exactamente 10 dígitos.',
                'phone.unique' => 'Este número de teléfono ya está registrado.',

                'id_number.string' => 'La cédula debe ser una cadena.',
                'id_number.max' => 'La cédula no puede tener más de 20 caracteres.',
                'id_number.unique' => 'Esta cédula ya está registrada.',

                'password.required' => 'La contraseña es obligatoria.',
                'password.string' => 'La contraseña debe ser una cadena.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'password.confirmed' => 'La confirmación de la contraseña no coincide.',

                'accepted_terms.required' => 'Debes aceptar los términos y condiciones.',
                'accepted_terms.accepted' => 'Debes aceptar los términos y condiciones.', 
            ]
        );

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
                'phone' => $request->phone,
                'id_number' => $request->id_number,
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