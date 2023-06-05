<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Pdf;
use App\Models\File;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PdfController extends Controller
{
    /**
     * Display a listing of the resource.
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
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:25000|mimes:pdf',
            'session_id' => 'required|exists:sessions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        // obtener el archivo del campo 'file' 
        $file = $request['file'];

        // verificación del tamaño del archivo
        if ($file->getSize() > 25000) {
            return response()->json(['message' => 'El tamaño del archivo excede el límite permitido'], 400);
        }

        // almacenar el archivo en FileController
        $fileController = new FileController();
        $fileData = $fileController->store($file);

        // crear el registro PDF
        $pdf = new Pdf();
        $pdf->file_id = $fileData['id'];
        $pdf->session_id = $request->session_id;
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

        return response()->json($pdf);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
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

        // obtener el archivo pdf
        $file = $pdf->file;

        // verificar que el usuario sea el propietario del pdf
        if ($file->user_id != $user->id) {
            return response()->json(['message' => 'No tienes permisos para eliminar este PDF'], 403);
        }

        // eliminar el registro del pdf y el archivo asociado
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

        // obtener el archivo pdf
        $file = $pdf->file;

        // verificar que el archivo exista en el sistema de almacenamiento
        if (!Storage::disk('local')->exists($file->path)) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }

        // respuesta
        return Storage::download($file->path, $file->filename);
    }
}
