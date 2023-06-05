<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ServiceResquest;
use App\Http\Resources\V1\ServiceCollection;
use App\Http\Controllers\Api\V1\FileController;

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
        // obtención del usuario autenticado
        $user = Auth::user();
        
        // crear el servicio
        $service = new Service();
        $service->name = $request->name;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->user_id = $user->id;
        $service->save();

        

        // agregar las imágenes al servicio usando el FileController
        if ($request->has('images')) {
            $fileController = new FileController();

            foreach ($request->images as $imageData) {
                $fileData = $fileController->store($imageData['url']);

                // crear la relación entre la imagen y el servicio
                $service->images()->create([
                    'url' => $fileData['url'],
                    'file_path' => $fileData['file_path'],
                ]);
            }
        }
        
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

        // eliminar las imágenes existentes del servicio
        $service->images()->delete();

        // agregar las nuevas imágenes al servicio usando el FileController
        if ($request->has('images')) {
            $fileController = new FileController();

            foreach ($request->images as $imageData) {
                $fileData = $fileController->store($imageData['url']);

                // crear la relación entre la imagen y el servicio
                $service->images()->create([
                    'url' => $fileData['url'],
                    'file_path' => $fileData['file_path'],
                ]);
            }
        }


        // respuesta
        return response()->json($service);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        // eliminar imágenes del servicio
        $service->images()->delete();

        // desconectar el servicio del usuario
        $user = User::find($service->user_id);
        $user->services()->detach($service);

        // eliminar el servicio
        $service->delete();
        
        // respuesta
        return response()->json([
            'message' => 'Servicio eliminado exitosamente'
        ]);
    }
}
