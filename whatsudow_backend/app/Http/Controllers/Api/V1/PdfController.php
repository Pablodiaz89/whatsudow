<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Pdf;
use App\Models\File;
use Illuminate\Http\Request;
use App\Http\Requests\PdfRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\V1\PdfResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\V1\PdfCollection;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="PDFs",
 *     description="API Endpoints para manejar los archivos pdf"
 * )
 */

class PdfController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/api/v1/pdfs",
     *     summary="Lista de archivos PDF",
     *     tags={"PDFs"},
     *     @OA\Response(response="200", description="Éxito"),
     * )
     */

    public function index()
    {
        $pdfs = Pdf::all();

        return new PdfCollection($pdfs);
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     *     path="/api/v1/pdfs",
     *     summary="Actualizar PDF",
     *     tags={"PDFs"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"file", "session_id"},
     *                 @OA\Property(
     *                     property="file",
     *                     description="Archivo PDF para actualizar",
     *                     type="file"
     *                 ),
     *                 @OA\Property(
     *                     property="session_id",
     *                     description="Session ID",
     *                     type="string"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="PDF subido exitosamente"),
     *     @OA\Response(response="400", description="El tamaño del archivo excede el límite permitido"),
     * )
     */

    public function store(PdfRequest $request) // almacena un nuevo pdf en la base de datos a través del controlador FileController que lo almacena
    {
        $validatedData = $request->validated();

        // obtiene el archivo 
        $file = $validatedData['file'];

        // verifica el tamaño del archivo
        if ($file->getSize() > 25000) {
            return response()->json(['message' => 'El tamaño del archivo excede el límite permitido'], 400);
        }

        // almacena el archivo en FileController
        $fileController = new FileController();
        $fileData = $fileController->store($file);

        // crea el registro PDF
        $pdf = new Pdf();
        $pdf->file_id = $fileData['id'];
        $pdf->session_id = $validatedData['session_id'];
        $pdf->save();

        return response()->json(['message' => 'PDF subido exitosamente']);
    }

    /**
     * Display the specified resource.
     */

    /**
     * @OA\Get(
     *     path="/api/v1/pdfs/{id}",
     *     summary="Obtener PDF",
     *     tags={"PDFs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="PDF ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Éxito"),
     *     @OA\Response(response="404", description="PDF no encontrado"),
     * )
     */

    public function show(string $id)
    {
        $pdf = Pdf::find($id);

        if (!$pdf) {
            return response()->json(['message' => 'PDF no encontrado'], 404);
        }

        return new PdfResource($pdf);
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * @OA\Put(
     *     path="/api/v1/pdfs/{id}",
     *     summary="Actualizar PDF",
     *     tags={"PDFs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="PDF ID",
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
     *                 required={"file", "session_id"},
     *                 @OA\Property(
     *                     property="file",
     *                     description="Archivo PDF actualizado",
     *                     type="file"
     *                 ),
     *                 @OA\Property(
     *                     property="session_id",
     *                     description="Session ID",
     *                     type="string"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="PDF actualizado exitosamente"),
     *     @OA\Response(response="400", description="El tamaño del archivo excede el límite permitido"),
     *     @OA\Response(response="404", description="PDF no encontrado"),
     * )
     */

    public function update(PdfRequest $request, string $id) // actualiza los datos de un pdf y los actualiza en el sistema de almacenamiento a través de FileController
    {
        $validatedData = $request->validated();

        $pdf = Pdf::find($id);

        if (!$pdf) {
            return response()->json(['message' => 'PDF no encontrado'], 404);
        }

        // obtiene el archivo 
        $file = $validatedData['file'];

        // verifica el tamaño del archivo
        if ($file->getSize() > 25000) {
            return response()->json(['message' => 'El tamaño del archivo excede el límite permitido'], 400);
        }

        // almacena el archivo en FileController
        $fileController = new FileController();
        $fileData = $fileController->store($file);

        // actualiza el registro PDF
        $pdf->file_id = $fileData['id'];
        $pdf->session_id = $validatedData['session_id'];
        $pdf->save();

        return response()->json(['message' => 'PDF actualizado exitosamente']);
    }

    /**
     * Remove the specified resource from storage.
     */

    /**
     * @OA\Delete(
     *     path="/api/v1/pdfs/{id}",
     *     summary="Eliminar PDF",
     *     tags={"PDFs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="PDF ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="PDF eliminado exitosamente"),
     *     @OA\Response(response="403", description="No tienes permisos para eliminar este PDF"),
     *     @OA\Response(response="404", description="PDF no encontrado"),
     * )
     */

    public function destroy($id, FileController $fileController)
    {
        $user = Auth::user();

        $pdf = Pdf::find($id);

        if (!$pdf) {
            return response()->json(['message' => 'PDF no encontrado'], 404);
        }

        // obtiene el archivo pdf
        $file = $pdf->file;

        // verifica que el usuario sea el propietario del pdf
        if ($file->user_id != $user->id) {
            return response()->json(['message' => 'No tienes permisos para eliminar este PDF'], 403);
        }

        // elimina el registro del pdf y el archivo asociado
        $pdf->delete();
        $fileController->destroy($file->id);

        return response()->json(['message' => 'PDF eliminado exitosamente']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/pdfs/{id}/download",
     *     summary="Descargar PDF",
     *     tags={"PDFs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="PDF ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Éxito"),
     *     @OA\Response(response="404", description="PDF no encontrado"),
     * )
     */

    public function download($id) // descarga un pdf específico
    {
        $pdf = Pdf::find($id);

        if (!$pdf) {
            return response()->json(['message' => 'PDF no encontrado'], 404);
        }

        // obtiene el archivo pdf
        $file = $pdf->file;

        // verifica que el archivo exista en el sistema de almacenamiento
        if (!Storage::disk('local')->exists($file->path)) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }

        // respuesta
        return Storage::download($file->path, $file->filename);
    }
}
