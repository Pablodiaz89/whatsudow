<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $files = File::all();
        return response()->json($files);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:25000',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $type = $file->getMimeType();
        $filename = time() . '_' . uniqid() . '.' . $extension;

        $path = $request->user()->id . '/' . $type . '/' . $filename;

        Storage::disk('local')->put($path, file_get_contents($file));

        $fileModel = new File();
        $fileModel->filename = $filename;
        $fileModel->path = $path;
        $fileModel->type = $type;
        $fileModel->user_id = $request->user()->id;
        $fileModel->save();

        return response()->json(['message' => 'Archivo subido exitosamente']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }

        return response()->json($file);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:25000',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $type = $file->getMimeType();
        $filename = time() . '_' . uniqid() . '.' . $extension;

        $path = $request->user()->id . '/' . $type . '/' . $filename;

        Storage::disk('local')->put($path, file_get_contents($file));

        $fileModel = new File();
        $fileModel->filename = $filename;
        $fileModel->path = $path;
        $fileModel->type = $type;
        $fileModel->user_id = $request->user()->id;
        $fileModel->save();

        return response()->json(['message' => 'Archivo subido exitosamente']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }

        Storage::disk('local')->delete($file->path);
        $file->delete();

        return response()->json(['message' => 'Archivo eliminado exitosamente']);
    }
}
