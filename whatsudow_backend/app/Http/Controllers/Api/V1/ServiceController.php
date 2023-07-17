<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ServiceResquest;
use App\Http\Resources\V1\ServiceResource;
use App\Http\Resources\V1\ServiceCollection;

/**
 * @OA\Tag(
 *     name="Services",
 *     description="API Endpoints para los servicios"
 * )
 */

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/api/v1/services",
     *     summary="Obtener todos los servicios",
     *     tags={"Services"},
     *     @OA\Response(response="200", description="Éxito"),
     *     @OA\Response(response="401", description="No autenticado")
     * )
     */

    public function index() 
    {
        $services = Service::all();
        return new ServiceCollection($services);
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     *     path="/api/v1/services",
     *     summary="Crear un nuevo servicio",
     *     tags={"Services"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="price", type="number")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Éxito"),
     *     @OA\Response(response="401", description="No autenticado"),
     *     @OA\Response(response="422", description="Datos de solicitud no válidos")
     * )
     */

    public function store(ServiceResquest $request) 
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

    /**
     * @OA\Get(
     *     path="/api/v1/services/{id}",
     *     summary="Obtener detalles de un servicio",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del servicio",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Éxito"),
     *     @OA\Response(response="404", description="Servicio no encontrado")
     * )
     */

    public function show(Service $service) 
    {
        return new ServiceResource($service);
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * @OA\Put(
     *     path="/api/v1/services/{id}",
     *     summary="Actualizar un servicio",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del servicio",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="price", type="number")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Éxito"),
     *     @OA\Response(response="401", description="No autenticado"),
     *     @OA\Response(response="404", description="Servicio no encontrado"),
     *     @OA\Response(response="422", description="Datos de solicitud no válidos")
     * )
     */

    public function update(ServiceResquest $request, Service $service) 
    {
        $data = $request->validated();

        $service->update($data);

        return new ServiceResource($service);
    }

    /**
     * Remove the specified resource from storage.
     */

    /**
     * @OA\Delete(
     *     path="/api/v1/services/{id}",
     *     summary="Eliminar un servicio",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del servicio",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Servicio eliminado exitosamente"),
     *     @OA\Response(response="401", description="No autenticado"),
     *     @OA\Response(response="404", description="Servicio no encontrado")
     * )
     */

    public function destroy(Service $service) 
    {
        $service->delete();

        return response()->json([
            'message' => 'Servicio eliminado exitosamente'
        ]);
    }
}
