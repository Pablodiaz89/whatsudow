<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Avatar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AvatarRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UploadFileRequest;
use App\Http\Resources\V1\AvatarResource;

class AvatarController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
