<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Favorite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // añadir a favoritos
    public function addFavorite(Request $request)
    {
        $userId = Auth::id();
        $serviceId = $request->input('service_id');

        // verificación si el servicio ya está en favoritos 
        $existingFavorite = Favorite::where('user_id', $userId)
            ->where('service_id', $serviceId)
            ->exists();

        if ($existingFavorite) {
            return response()->json(['message' => 'El servicio ya está en favoritos']);
        }

        // crear un nuevo registro de favorito
        $favorite = new Favorite();
        $favorite->user_id = $userId;
        $favorite->service_id = $serviceId;
        $favorite->save();

        return response()->json(['message' => 'Servicio agregado a favoritos']);
    }


    // eliminar todos los favoritos
    public function removeAllFavorites()
    {
        $userId = Auth::id();
        Favorite::where('user_id', $userId)->delete();

        return response()->json(['message' => 'Todos los favoritos fueron eliminados']);
    }

    // eliminar un favoritos especificio
    public function removeSingleFavorite($favoriteId)
    {
        $userId = Auth::id();
        Favorite::where('user_id', $userId)
            ->where('id', $favoriteId)
            ->delete();

        return response()->json(['message' => 'Favorito eliminado']);
    }

    // ver favoritos
    public function getFavorites()
    {
        $userId = Auth::id();
        $favorites = Favorite::where('user_id', $userId)
        ->with('service.user', 'user.category', 'service.images')
        ->get();

    return response()->json($favorites);
    }
}
