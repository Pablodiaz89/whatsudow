<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginResquest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\RegisterProviderResquest;
use App\Http\Requests\RegisterOrganizerResquest;

// este controlador controla el sistema de registro tanto de proveedores como organizadores, el login y la recuperación de contraseña

class AuthController extends Controller
{
    // REGISTRO PARA: Proveedores
    public function registerprovider(RegisterProviderResquest $request)
    {

        // validar el registro
        $data = $request->validated();

        // crear el usuario
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->phone()->create([
            'phone' => $data['phone'],
        ]);

        // guardar
        $user->save();

        // asignar rol
        $user->assignRole('proveedor');

        // asignar token
        $token = $user->createToken('auth_token')->plainTextToken;

        // respuesta
        return response()->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer',], 200);
    }



    // REGISTRO PARA: Organizadores
    public function registerorganizer(RegisterOrganizerResquest $request)
    {

        // validar el registro
        $data = $request->validated();

        // crear el usuario
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->phone()->create([
            'phone' => $data['phone'],
        ]);

        $user->company()->create([
            'company' => $data['company']
        ]);

        // guardar
        $user->save();

        // asignar rol
        $user->assignRole('organizador');

        // asignar token
        $token = $user->createToken('auth_token')->plainTextToken;

        // respuesta
        return response()->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer',], 200);
    }


    // LOGIN
    public function login(LoginResquest $request)
    {
        // validar la autenticación
        $data = $request->validated();

        // revisar el password
        if (!Auth::attempt($data)) {
            return response([
                'errors' => ['El email o el password son incorrectos']
            ], 422);
        }

        // autenticar el usuario
        $user = User::where('email', $request['email'])->firstOrFail();

        // asignar token
        $token = $user->createToken('auth_token')->plainTextToken;

        // respuesta
        return response()->json([
            'messaje' => 'Hola ' . $user->name,
            'accessToken' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 200);
    }

    // CIERRE DE SESIÓN - LOGOUT 
    public function logout(Request $request)
    {
        // obtener el usuario autenticado
        $user = $request->user();

        // verificar si el usuario esta autenticado
        if ($user) {
            // revocar todos los tokens de acceso del usuario
            $user->tokens()->delete();

            // cerrar sesión
            Auth::guard('web')->logout();
        }

        return response()->json([
            'message' => 'Ha cerrado sesión correctamente'
        ], 200);
    }

    // recuperación de contraseña
    public function forgotPassword(Request $request)
    {
        // validar la solicitud
        $request->validate([
            'email' => 'required|email',
        ]);

        // buscar al usuario por su dirección de correo electrónico
        $user = User::where('email', $request->email)->first();

        // verificar si se encontró un usuario
        if (!$user) {
            return response()->json(['message' => 'No se encontró ningún usuario con esa dirección de correo electrónico'], 404);
        }

        // generar un token de restablecimiento de contraseña
        $token = Password::createToken($user);

        // enviar el correo electrónico de restablecimiento de contraseña
        $user->sendPasswordResetNotification($token);

        return response()->json(['message' => 'Se ha enviado un correo electrónico para restablecer la contraseña'], 200);
    }
}
