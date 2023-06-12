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

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $files = File::all();
        return FileResource::collection($files);
    }

    /**
     * Store a newly created resource in storage.
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
