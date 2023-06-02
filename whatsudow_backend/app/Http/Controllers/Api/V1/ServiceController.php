<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ServiceResquest;
use App\Http\Resources\V1\ServiceCollection;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // muestra todos los servicios
        return new ServiceCollection(Service::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceResquest $request)
    {
        // obtener el usuario autenticado
        $user = Auth::user();
        
        // crear el servicio
        $service = new Service();
        $service->name = $request->name;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->user_id = $user->id;
        $service->save();
        
        // respuesta
        return response()->json($service, 201); 
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        // respuesta
        return response()->json($service);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceResquest $request, Service $service)
    {
        // validación
        $data = $request->validated();

        // actualización de servicios
        $service->update($data);

        // respuesta
        return response()->json($service);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        // eliminar
        $service->delete();
        
        // respuesta
        return response()->json([
            'message' => 'Servicio eliminado exitosamente'
        ]);
    }
}
