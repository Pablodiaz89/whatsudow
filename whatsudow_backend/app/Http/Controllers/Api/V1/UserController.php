<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints para los datos de usuario, como: nombre, email y contraseña"
 * )
 */


class UserController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/v1/user/{id}",
     *     summary="Obtener perfil de usuario",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del usuario",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Éxito"),
     *     @OA\Response(response="404", description="Usuario no encontrado")
     * )
     */

    public function showProfile($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($user);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/user/name/{id}",
     *     summary="Actualizar nombre de usuario",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del usuario",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Nuevo nombre del usuario",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Nombre actualizado correctamente"),
     *     @OA\Response(response="404", description="Usuario no encontrado")
     * )
     */

    public function updateName(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'name' => 'required|string',
        ]);

        $user->name = $request->input('name');
        $user->save();

        return response()->json(['message' => 'Nombre actualizado correctamente']);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/email/{id}",
     *     summary="Actualizar email de usuario",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del usuario",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Nuevo email del usuario",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Correo electrónico actualizado con éxito"),
     *     @OA\Response(response="404", description="Usuario no encontrado")
     * )
     */

    public function updateEmail(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $userId,
        ]);

        $user->email = $request->input('email');
        $user->save();

        return response()->json(['message' => 'Correo electrónico actualizado con éxito']);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/user/password/{id}",
     *     summary="Actualizar contraseña de usuario",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del usuario",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Nueva contraseña del usuario",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="password"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Contraseña actualizada exitosamente"),
     *     @OA\Response(response="404", description="Usuario no encontrado")
     * )
     */

    public function updatePassword(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user->password = bcrypt($request->input('password'));
        $user->save();

        return response()->json(['message' => 'Contraseña actualizada exitosamente']);
    }
}
