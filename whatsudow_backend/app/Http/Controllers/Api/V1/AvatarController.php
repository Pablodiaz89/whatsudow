<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Avatar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AvatarRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UploadFileRequest;
use App\Http\Resources\V1\AvatarResource;

/**
 * @OA\Tag(
 *     name="Avatars",
 *     description="Endpoints para los avatars o imagenes de perfil"
 * )
 */

class AvatarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/avatars",
     *     operationId="indexAvatars",
     *     tags={"Avatars"},
     *     summary="Obtener una lista de avatares",
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Schema(ref="#/components/schemas/AvatarResource")
     *             )
     *         )
     *     )
     * )
     */

    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Muestra el avatar específico
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/avatars/{id}",
     *     operationId="showAvatar",
     *     tags={"Avatars"},
     *     summary="Obtener el avatar específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the avatar",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="avatar", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="file", type="string", format="binary"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Avatar no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */

    public function show(string $id)
    {
        $user = Auth::user();

        if (!$user->avatar) {
            return response()->json(['message' => 'Avatar no encontrado'], 404);
        }

        return new AvatarResource($user->avatar);
    }

    /**
     * Actualizar el avatar específico en el almacenamiento.
     *
     * @param  \Illuminate\Http\UploadFileRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/avatars/{id}",
     *     operationId="updateAvatar",
     *     tags={"Avatars"},
     *     summary="Actualiza el avatar de un usuario en concreto. Lo almacena usando el controlador FileController",
     *     @OA\Parameter(
     *         name="id",
     *         description="Avatar ID",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="file", type="file", description="El archivo de avatar actualizado")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Avatar actualizado con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="El tamaño del archivo supera el límite permitido",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */

    public function update(UploadFileRequest $request)
    {
        $user = Auth::user();

        $validatedData = $request->validated();

        // obtiene el archivo 
        $file = $validatedData['file'];

        // verifica el tamaño del archivo
        if ($file->getSize() > 25000) {
            return response()->json(['message' => 'El tamaño del archivo supera el límite permitido'], 400);
        }

        // almacena el archivo en FileController
        $fileController = new FileController();
        $fileData = $fileController->store($request);

        // actualiza el avatar del usuario
        $avatar = $user->avatar;

        if (!$avatar) {
            $avatar = Avatar::create(['file_id' => $fileData['id'], 'user_id' => $user->id]);
        } else {
            $avatar->file_id = $fileData['id'];
            $avatar->save();
        }

        return response()->json(['message' => 'Avatar actualizado con éxito']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
