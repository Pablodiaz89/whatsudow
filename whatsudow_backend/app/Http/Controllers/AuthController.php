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

/**
 * @OA\Info(
 *             title="Whatsudow", 
 *             version="1.0",
 *             description="Aplicación que permite a los organizadores de eventos unificar los datos de sus proveedores de servicios. Con esta app, los organizadores de eventos pueden almacenar y acceder a información importante de los proveedores, comocalendarios de disponibilidad, PDF actualizados, cláusulas y datos de contacto."
 * )
 *
 * @OA\Server(url="http://127.0.0.1:8000")
 */

// este controlador controla el sistema de registro tanto de proveedores como organizadores, el login y la recuperación de contraseña

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/registerprovider",
     *     summary="Registro para proveedores",
     *     operationId="registerProvider",
     *     tags={"Autenticación"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="phone", type="string")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Usuario registrado correctamente"),
     *     @OA\Response(response="422", description="Datos de entrada inválidos")
     * )
     */

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

    /**
     * @OA\Post(
     *     path="/registerorganizer",
     *     summary="Registro para organizadores",
     *     operationId="registerOrganizer",
     *     tags={"Autenticación"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="company", type="string")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Usuario registrado correctamente"),
     *     @OA\Response(response="422", description="Datos de entrada inválidos")
     * )
     */

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

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Iniciar sesión",
     *     operationId="login",
     *     tags={"Autenticación"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Usuario logeado correctamente"),
     *     @OA\Response(response="422", description="El email o el password son incorrectos")
     * )
     */

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

    /**
     * @OA\Get(
     *     path="/logout",
     *     summary="Cerrar sesión",
     *     operationId="logout",
     *     tags={"Autenticación"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Ha cerrado sesión correctamente")
     * )
     */

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

    /**
     * @OA\Post(
     *     path="/forgot-password",
     *     summary="Recuperar contraseña",
     *     operationId="forgotPassword",
     *     tags={"Autenticación"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Se ha enviado un correo electrónico para restablecer la contraseña"),
     *     @OA\Response(response="404", description="No se encontró ningún usuario con esa dirección de correo electrónico")
     * )
     */

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
