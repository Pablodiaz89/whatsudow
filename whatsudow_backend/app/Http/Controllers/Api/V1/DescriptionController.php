<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Description;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DescriptionRequest;
use App\Http\Resources\V1\DescriptionResource;
use App\Http\Resources\V1\DescriptionCollection;

/**
 * @OA\Tag(
 *     name="Descriptions",
 *     description="Endpoints para las descripciones del usuario"
 * )
 */

class DescriptionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     *     path="/descriptions",
     *     operationId="getDescriptions",
     *     tags={"Descriptions"},
     *     summary="Obtener todas las descripciones",
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="description", type="string", example="Descripción de la muestra")
     *             )
     *         )
     *     )
     * )
     */

    public function index()
    {
        // obtiene todas las descripciones
        return new DescriptionCollection(Description::all());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @OA\Post(
     *     path="/descriptions",
     *     operationId="storeDescription",
     *     tags={"Descriptions"},
     *     summary="Guardar una nueva descripción",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="description", type="string", example="Descripción de la muestra")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Descripción creada con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="description", type="string", example="Descripción de la muestra")
     *         )
     *     )
     * )
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
     *
     * @param  \App\Models\Description  $description
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     *     path="/descriptions/{id}",
     *     operationId="showDescription",
     *     tags={"Descriptions"},
     *     summary="Obtener una descripción específica",
     *     @OA\Parameter(
     *         name="id",
     *         description="ID descripción",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="description", type="string", example="Descripción de la muestra")
     *         )
     *     )
     * )
     */

    public function show(Description $description)
    {
        // responde con la descripción solicitada
        return new DescriptionResource($description);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Description  $description
     * @return \Illuminate\Http\Response
     *
     * @OA\Put(
     *     path="/descriptions/{id}",
     *     operationId="updateDescription",
     *     tags={"Descriptions"},
     *     summary="Actualizar una descripción específica",
     *     @OA\Parameter(
     *         name="id",
     *         description="ID descripción",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="description", type="string", example="Descripción actualizada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Description updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="description", type="string", example="Descripción actualizada")
     *         )
     *     )
     * )
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
     *
     * @param  \App\Models\Description  $description
     * @return \Illuminate\Http\Response
     *
     * @OA\Delete(
     *     path="/descriptions/{id}",
     *     operationId="deleteDescription",
     *     tags={"Descriptions"},
     *     summary="Eliminar una descripción específica",
     *     @OA\Parameter(
     *         name="id",
     *         description="ID descripción",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Descripción eliminada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
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
