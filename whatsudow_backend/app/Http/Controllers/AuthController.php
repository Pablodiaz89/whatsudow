<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginResquest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterProviderResquest;
use App\Http\Requests\RegisterOrganizerResquest;

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
        return response()->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer', ],200); 

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
        return response()->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer', ],200); 

    }


                                                    // LOGIN
    public function login(LoginResquest $request)
    {
        // validar la autenticación
        $data = $request->validated();

        // revisar el password
        if(!Auth::attempt($data)){
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
            'messaje' => 'Hola '.$user->name,
            'accessToken' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ],200);
    }

                                        // comprobar que esta en sesion
    public function infouser(Request $request)
    {
        return $request->user();
    }

                                                    // CIERRE DE SESIÓN - LOGOUT 
    public function logout(Request $request)
    {
        // obtener el usuario autenticado
    $user = $request->user;

    // Revocar todos los tokens de acceso del usuario
    $user->tokens()->delete();

    // Cerrar sesión
    auth()->logout();

    return response()->json([
        'message' => 'Ha cerrado sesión correctamente'
    ], 200);

    }
}
