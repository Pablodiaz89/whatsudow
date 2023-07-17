<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\File;
use App\Models\Gallery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\FileResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UploadFileRequest;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Files",
 *     description="API Endpoints para el sistema de almacenamiento de archivos"
 * )
 */

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/api/v1/files",
     *     summary="Obtener todos los archivos",
     *     tags={"Files"},
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Schema(
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example="1"),
     *                         @OA\Property(property="filename", type="string", example="example.jpg"),
     *                         @OA\Property(property="path", type="string", example="/path/to/file.jpg"),
     *                         @OA\Property(property="type", type="string", example="image/jpeg"),
     *                         @OA\Property(property="user_id", type="integer", example="123"),
     *                         @OA\Property(property="gallery_id", type="integer", example="456"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2023-07-15 12:34:56"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2023-07-15 12:34:56")
     *                     )
     *                 )
     *             )
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

    public function index()
    {
        $files = File::all();
        return FileResource::collection($files);
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     *     path="/api/v1/files",
     *     summary="Almacenar un nuevo archivo",
     *     tags={"Files"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Schema(
     *                 @OA\Property(property="file", type="string", format="binary"),
     *                 @OA\Property(property="gallery_id", type="integer", example="123")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="filename", type="string", example="example.jpg"),
     *             @OA\Property(property="path", type="string", example="/path/to/file.jpg"),
     *             @OA\Property(property="type", type="string", example="image/jpeg"),
     *             @OA\Property(property="user_id", type="integer", example="123"),
     *             @OA\Property(property="gallery_id", type="integer", example="456"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-07-15 12:34:56"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-07-15 12:34:56")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No autenticado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error al guardar el archivo",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="file", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="file", type="string", example="The file field is required.")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function store(UploadFileRequest $request)
    {
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $type = $file->getMimeType();
        $filename = time() . '_' . uniqid() . '.' . $extension;

        $path = $request->user()->id . '/' . $type . '/' . $filename;

        try {
            Storage::disk('local')->put($path, file_get_contents($file));
        } catch (\Exception $e) {
            abort(500, 'Error al guardar el archivo');
        }

        $fileModel = new File();
        $fileModel->filename = $filename;
        $fileModel->path = $path;
        $fileModel->type = $type;
        $fileModel->user_id = $request->user()->id;
        $fileModel->save();

        if ($request->has('gallery_id')) {
            $gallery = Gallery::find($request->gallery_id);
            if ($gallery) {
                $fileModel->gallery()->associate($gallery);
                $fileModel->save();
            }
        }

        return new FileResource($fileModel);
    }

    /**
     * Display the specified resource.
     */

    /**
     * @OA\Get(
     *     path="/api/v1/files/{id}",
     *     summary="Mostrar un archivo específico",
     *     tags={"Files"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID Archivo",
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
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="filename", type="string", example="example.jpg"),
     *             @OA\Property(property="path", type="string", example="/path/to/file.jpg"),
     *             @OA\Property(property="type", type="string", example="image/jpeg"),
     *             @OA\Property(property="user_id", type="integer", example="123"),
     *             @OA\Property(property="gallery_id", type="integer", example="456"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-07-15 12:34:56"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-07-15 12:34:56")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No autenticado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Archivo no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="File not found.")
     *         )
     *     )
     * )
     */

    public function show(string $id)
    {
        $file = File::find($id);

        if (!$file) {
            abort(404, 'Archivo no encontrado');
        }

        return new FileResource($file);
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * @OA\Put(
     *     path="/api/v1/files/{id}",
     *     summary="Actualizar un archivo específico",
     *     tags={"Files"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID Archivo",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example="1"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos de archivo actualizados",
     *         @OA\JsonContent(
     *             @OA\Property(property="file", type="string", format="binary"),
     *             @OA\Property(property="gallery_id", type="integer", example="456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="filename", type="string", example="example.jpg"),
     *             @OA\Property(property="path", type="string", example="/path/to/file.jpg"),
     *             @OA\Property(property="type", type="string", example="image/jpeg"),
     *             @OA\Property(property="user_id", type="integer", example="123"),
     *             @OA\Property(property="gallery_id", type="integer", example="456"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-07-15 12:34:56"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-07-15 12:34:56")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No autenticado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Archivo no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="File not found.")
     *         )
     *     )
     * )
     */

    public function update(UploadFileRequest $request, string $id)
    {
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $type = $file->getMimeType();
        $filename = time() . '_' . uniqid() . '.' . $extension;

        $path = $request->user()->id . '/' . $type . '/' . $filename;

        try {
            Storage::disk('local')->put($path, file_get_contents($file));
        } catch (\Exception $e) {
            abort(500, 'Error al guardar el archivo');
        }

        $fileModel = File::find($id);

        if (!$fileModel) {
            abort(404, 'Archivo no encontrado');
        }

        Storage::disk('local')->delete($fileModel->path);

        $fileModel->filename = $filename;
        $fileModel->path = $path;
        $fileModel->type = $type;
        $fileModel->user_id = $request->user()->id;
        $fileModel->save();

        if ($request->has('gallery_id')) {
            $gallery = Gallery::find($request->gallery_id);
            if ($gallery) {
                $fileModel->gallery()->associate($gallery);
                $fileModel->save();
            }
        }

        return new FileResource($fileModel);
    }

    /**
     * Remove the specified resource from storage.
     */

    /**
     * @OA\Delete(
     *     path="/api/v1/files/{id}",
     *     summary="Eliminar un archivo",
     *     tags={"Files"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID Archivo",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Archivo eliminado exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No autenticado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Archivo no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Archivo no encontrado")
     *         )
     *     )
     * )
     */

    public function destroy(string $id)
    {
        $file = File::find($id);

        if (!$file) {
            abort(404, 'Archivo no encontrado');
        }

        Storage::disk('local')->delete($file->path);
        $file->delete();

        return ['message' => 'Archivo eliminado exitosamente'];
    }
}
