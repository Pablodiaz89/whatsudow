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

class PdfController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pdfs = Pdf::all();

        return new PdfCollection($pdfs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PdfRequest $request)
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
    public function update(PdfRequest $request, string $id)
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

    public function download($id)
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
