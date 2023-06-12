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


class GalleryController extends Controller
{
    

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // verificación si el usuario tiene el rol "proveedor"
        if ($user->hasRole('proveedor')) {
            // obtener las galerías del usuario
            $galleries = $user->galleries;
        } else {
            // obtener las galerías de los usuarios con el rol "proveedor"
            $galleries = Gallery::whereHas('user', function ($query) {
                $query->whereHas('roles', function ($query) {
                    $query->where('name', 'proveedor');
                });
            })->get();
        }

        return new GalleryCollection($galleries);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GalleryRequest $request)
    {
        $user = Auth::user();

        // verificación si el usuario tiene el permiso "crear galería"
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
    public function destroy(Gallery $gallery)
    {$user = Auth::user();

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
