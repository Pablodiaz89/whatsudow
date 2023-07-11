<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ServiceResquest;
use App\Http\Resources\V1\ServiceResource;
use App\Http\Resources\V1\ServiceCollection;

// este controlador controla los servicios 

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() // muestra los servicios
    {
        $services = Service::all();
        return new ServiceCollection($services);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceResquest $request) // almacena un nuevo servicio
    {
        $user = Auth::user();

        $service = new Service();
        $service->name = $request->name;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->user_id = $user->id;
        $service->save();

        return new ServiceResource($service);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service) // muestra un servicio específicio
    {
        return new ServiceResource($service);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceResquest $request, Service $service) // actualiza un servicio específico
    {
        $data = $request->validated();

        $service->update($data);

        return new ServiceResource($service);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service) // elimina un servicio específico
    {
        $service->delete();

        return response()->json([
            'message' => 'Servicio eliminado exitosamente'
        ]);
    }
}
