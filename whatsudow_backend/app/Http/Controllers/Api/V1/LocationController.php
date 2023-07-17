<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\V1\LocationResource;
use App\Http\Resources\V1\LocationCollection;

/**
 * @OA\Tag(
 *     name="Locations",
 *     description="API Endpoints para localizaciones de ubicación. ... para actualizar o crear si no existe esta relacionada en el controlador BudgetController de presupuesto"
 * )
 */

class LocationController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/api/v1/locations",
     *     summary="Obtener todas las ubicaciones",
     *     tags={"Locations"},
     *     @OA\Response(response="200", description="Éxito"),
     * )
     */

    public function index() 
    {
        $locations = Location::all();

        return new LocationCollection($locations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/api/v1/locations/{id}",
     *     summary="Obtener una ubicación específica",
     *     tags={"Locations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID Localización",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Éxito"),
     *     @OA\Response(response="404", description="Ubicación no encontrada")
     * )
     */

    public function show(string $id) 
    {
        $location = Location::findOrFail($id);

        return new LocationResource($location);
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
    public function destroy(string $id)
    {
    }
}
