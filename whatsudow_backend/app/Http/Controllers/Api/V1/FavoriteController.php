<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Favorite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Favorites",
 *     description="API Endpoints para el sistema de favoritos"
 * )
 */

class FavoriteController extends Controller
{

    /**
     * Agregar un servicio a la lista de favoritos del usuario.
     *
     * @OA\Post(
     *     path="/api/v1/favorites",
     *     summary="Agregar un servicio a favoritos",
     *     tags={"Favorites"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="ID de servicio",
     *         @OA\JsonContent(
     *             @OA\Property(property="service_id", type="integer", example="123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Servicio agregado a favoritos")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No autenticado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El servicio ya está en favoritos")
     *         )
     *     )
     * )
     */

    public function addFavorite(Request $request)
    {
        $userId = Auth::id();
        $serviceId = $request->input('service_id');

        // verifica si el servicio ya está en favoritos 
        $existingFavorite = Favorite::where('user_id', $userId)
            ->where('service_id', $serviceId)
            ->exists();

        if ($existingFavorite) {
            return response()->json(['message' => 'El servicio ya está en favoritos']);
        }

        // crea un nuevo registro de favorito
        $favorite = new Favorite();
        $favorite->user_id = $userId;
        $favorite->service_id = $serviceId;
        $favorite->save();

        return response()->json(['message' => 'Servicio agregado a favoritos']);
    }

    /**
     * Eliminar todos los favoritos del usuario.
     *
     * @OA\Delete(
     *     path="/api/v1/favorites",
     *     summary="Eliminar todos los favoritos",
     *     tags={"Favorites"},
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Todos los favoritos fueron eliminados")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No autenticado")
     *         )
     *     )
     * )
     */

    public function removeAllFavorites()
    {
        $userId = Auth::id();
        Favorite::where('user_id', $userId)->delete();

        return response()->json(['message' => 'Todos los favoritos fueron eliminados']);
    }

    /**
     * Eliminar un favorito específico del usuario.
     *
     * @OA\Delete(
     *     path="/api/v1/favorites/{favoriteId}",
     *     summary="Eliminar un favorito específico",
     *     tags={"Favorites"},
     *     @OA\Parameter(
     *         name="favoriteId",
     *         in="path",
     *         description="ID Favorito",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example="1"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Favorito eliminado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No autenticado")
     *         )
     *     )
     * )
     */

    public function removeSingleFavorite($favoriteId)
    {
        $userId = Auth::id();
        Favorite::where('user_id', $userId)
            ->where('id', $favoriteId)
            ->delete();

        return response()->json(['message' => 'Favorito eliminado']);
    }

    /**
     * @OA\Get(
     *     path="/favorites",
     *     operationId="getFavorites",
     *     tags={"Favorites"},
     *     summary="Obtener todos los favoritos del usuario",
     *     @OA\Response(
     *         response=200,
     *         description="Respuesta exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", format="int64"),
     *             @OA\Property(property="user_id", type="integer", format="int64"),
     *             @OA\Property(property="service_id", type="integer", format="int64"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     )
     * )
     */

    public function getFavorites()
    {
        $userId = Auth::id();
        $favorites = Favorite::where('user_id', $userId)
            ->with('service.user')
            ->get();

        return response()->json($favorites);
    }
}
