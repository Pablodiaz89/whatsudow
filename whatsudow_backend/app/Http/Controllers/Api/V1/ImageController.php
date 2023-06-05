<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
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
    public function store(Request $request, FileController $fileController)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:2000',
            'service_id' => 'required|exists:services,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }
    
        $fileData = $fileController->store($request);
    
        $image = new Image();
        $image->url = $fileData['url'];
        $image->file_path = $fileData['file_path'];
        $image->service_id = $request->service_id;
        $image->save();
    
        return response()->json($image, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        return response()->json($image);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Image $image, FileController $fileController)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:2000',
            'service_id' => 'required|exists:services,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }
    
        $fileData = $fileController->store($request);
    
        $image->url = $fileData['url'];
        $image->file_path = $fileData['file_path'];
        $image->service_id = $request->service_id;
        $image->save();
    
        return response()->json($image);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image, FileController $fileController)
    {
        $fileController->destroy($image->file_path);

        $image->delete();

        return response()->json(['message' => 'Imagen eliminada exitosamente']);
    }
}
