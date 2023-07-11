<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\V1\LocationResource;
use App\Http\Resources\V1\LocationCollection;

// este controlador maneja las localizaciones de ubicación. ... para actualizar o crear si no existe esta relacionada en el controlador BudgetController de presupuesto

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() // devuelve todas las ubicaciones
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
     */
    public function show(string $id) // muestra una ubicación específica
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
