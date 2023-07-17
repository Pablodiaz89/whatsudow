<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\File;
use App\Models\Gallery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\GalleryRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\V1\GalleryResource;
use App\Http\Resources\V1\GalleryCollection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

/**
 * @OA\Tag(
 *     name="Galleries",
 *     description="API Endpoints la subida de archivos de imagenes"
 * )
 */


class GalleryController extends Controller
{
    use HasRoles;

    /**
     * @OA\Get(
     *     path="/galleries",
     *     summary="Get all galleries",
     *     description="Recuperar todos los archivos de imagenes de la galerías",
     *     tags={"Galleries"},
     *     @OA\Response(
     *         response=200,
     *         description="Éxito",
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function index()
    {
        $user = Auth::user();

        // obteneción de los archivos de imagenes las galerías del usuario proveedor actualmente autenticado
        $galleries = $user->galleries;

        return new GalleryCollection($galleries);
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     *     path="/galleries",
     *     summary="Crear una nueva imagen en la galería",
     *     description="Crear una nueva imagen en la galería",
     *     tags={"Galleries"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="string", format="binary"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Imagen agregada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function store(GalleryRequest $request)
    {
        $user = Auth::user();


        // verificación si el usuario tiene el permiso "crear galería" (imagen)
        if (!$user->can('crear galería')) {
            return response()->json(['message' => 'No tienes permiso para añadir una imagen'], 403);
        }

        $gallery = new Gallery([
            'name' => $request->name,
            'user_id' => $user->id,
        ]);

        $gallery->save();

        $files = $request->file('images');

        foreach ($files as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $user->id . '/' . $filename;

            // almacena el archivo en el sistema de archivos
            Storage::disk('local')->put($path, file_get_contents($file));

            // crea una nueva entrada de archivo relacionada con la galería
            $fileModel = new File([
                'filename' => $filename,
                'path' => $path,
                'type' => 'image',
                'user_id' => $user->id,
                'gallery_id' => $gallery->id,
            ]);

            $fileModel->save();
        }

        return response()->json(['message' => 'Imagen agregada exitosamente']);
    }

    /**
     * Display the specified resource.
     */

    /**
     * @OA\Get(
     *     path="/galleries/{gallery}",
     *     summary="Obtener imagen en la galería por ID",
     *     description="Obtener imagen en la galería por ID",
     *     tags={"Galleries"},
     *     @OA\Parameter(
     *         name="gallery",
     *         in="path",
     *         description="Gallery ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function show(Gallery $gallery)
    {
        $user = Auth::user();

        // verificación de si el usuario tiene el rol 'organizador' y la galería pertenece a un usuario con el rol 'proveedor'
        if ($user->hasRole('organizador') && $gallery->user->hasRole('proveedor')) {
            return new GalleryResource($gallery);
        }

        // verificación de si el usuario tiene el permiso 'ver galería' (en caso de que el rol organizador pueda ver todas las galerías)
        if (!$user->can('ver galería')) {
            return response()->json(['message' => 'No tienes permisos para ver esta imagen'], 403);
        }

        return new GalleryResource($gallery);
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * @OA\Put(
     *     path="/galleries/{gallery}",
     *     summary="Actualizar galería",
     *     description="Actualizar galería",
     *     tags={"Galleries"},
     *     @OA\Parameter(
     *         name="gallery",
     *         in="path",
     *         description="Gallery ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Imagen actualizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function update(GalleryRequest $request, Gallery $gallery)
    {
        $user = Auth::user();

        // verificación de si el usuario tiene el permiso 'actualizar galería' o tiene el rol 'proveedor'
        if (!$user->can('actualizar galería') && !$user->hasRole('proveedor')) {
            return response()->json(['message' => 'No tienes permisos para actualizar esta imagen'], 403);
        }

        if ($gallery->user_id !== $user->id) {
            return response()->json(['message' => 'No tienes permisos para actualizar esta imagen'], 403);
        }

        $gallery->update([
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Imagen actualizada exitosamente']);
    }

    /**
     * Remove the specified resource from storage.
     */

    /**
     * @OA\Delete(
     *     path="/galleries/{gallery}",
     *     summary="Eliminar una imagen de la galería",
     *     description="Eliminar una imagen de la galería",
     *     tags={"Galleries"},
     *     @OA\Parameter(
     *         name="gallery",
     *         in="path",
     *         description="Gallery ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Gallery deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Imagen eliminada exitosamente"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permiso denegado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="No tienes permisos para eliminar esta imagen"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No tienes permisos para eliminar esta imagen",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="No tienes permisos para eliminar esta imagen"
     *             )
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function destroy(Gallery $gallery)
    {
        $user = Auth::user();

        // verificación si el usuario tiene el permiso 'eliminar galería' o tiene el rol 'proveedor'
        if (!$user->can('eliminar galería') && !$user->hasRole('proveedor')) {
            return response()->json(['message' => 'No tienes permisos para eliminar esta imagen'], 403);
        }

        if ($gallery->user_id !== $user->id) {
            return response()->json(['message' => 'No tienes permisos para eliminar esta imagen'], 403);
        }

        // elimina las imágenes del sistema de archivos
        foreach ($gallery->files as $file) {
            Storage::disk('local')->delete($file->path);
            $file->delete();
        }

        $gallery->delete();

        return response()->json(['message' => 'Imagen eliminada exitosamente']);
    }
}
