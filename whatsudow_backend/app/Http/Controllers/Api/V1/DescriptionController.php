<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Description;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DescriptionRequest;
use App\Http\Resources\V1\DescriptionResource;
use App\Http\Resources\V1\DescriptionCollection;

class DescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       // obtiene todas las descripciones
       return new DescriptionCollection(Description::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DescriptionRequest $request)
    {
        // obtiene el usuario autenticado
        $user = Auth::user();

        // crea la descripción
        $description = new Description();
        $description->description = $request->description;
        $description->user_id = $user->id;
        $description->save();

        // responde con la descripción creada
        return response()->json(new DescriptionResource($description), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Description $description)
    {
        // responde con la descripción solicitada
        return new DescriptionResource($description);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DescriptionRequest $request, Description $description)
    {
        // valida y actualiza la descripción
        $description->description = $request->description;
        $description->save();

        // responde con la descripción actualizada
        return new DescriptionResource($description);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Description $description)
    {
        // elimina la descripción
        $description->delete();

        // responde con un mensaje de éxito
        return response()->json([
            'message' => 'Descripción eliminada exitosamente'
        ]);
    }
}
