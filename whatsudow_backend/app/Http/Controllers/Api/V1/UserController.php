<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    // ver perfil del usuario
    public function showProfile($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($user);
    }


    // actualizar nombre
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


    // actualizar email
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


    // actualizar contraseña
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
