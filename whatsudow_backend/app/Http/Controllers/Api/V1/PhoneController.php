<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Phone;
use Illuminate\Http\Request;
use App\Http\Requests\PhoneRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\V1\PhoneResource;

/**
 * @OA\Tag(
 *     name="Phones",
 *     description="API Endpoints para los teléfonos de los usuarios"
 * )
 */


class PhoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/api/v1/phones",
     *     summary="Obtener lista de teléfonos",
     *     tags={"Phones"},
     *     @OA\Response(response="200", description="Éxito")
     * )
     */

    public function index()
    {
        $user = auth()->user();
        $phones = $user->phones;

        return PhoneResource::collection($phones);
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     *     path="/api/v1/phones",
     *     summary="Guardar teléfono",
     *     tags={"Phones"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="phone", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Éxito"),
     *     @OA\Response(response="422", description="Teléfono no válido")
     * )
     */

    public function store(PhoneRequest $request)
    {
        $user = auth()->user();

        $phone = new Phone();
        $phone->phone = $request->input('phone');
        $phone->user_id = $user->id;
        $phone->save();

        return new PhoneResource($phone);
    }

    /**
     * Display the specified resource.
     */

    /**
     * @OA\Get(
     *     path="/api/v1/phones/{id}",
     *     summary="Obtener teléfono",
     *     tags={"Phones"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del teléfono",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Éxito"),
     *     @OA\Response(response="404", description="No se encontró el teléfono del usuario")
     * )
     */

    public function show($id)
    {
        $user = auth()->user();
        $phone = $user->phone;

        if (!$phone) {
            return response()->json(['message' => 'No se encontró el teléfono del usuario'], 404);
        }

        return new PhoneResource($phone);
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * @OA\Put(
     *     path="/api/v1/phones/{id}",
     *     summary="Actualizar teléfono",
     *     tags={"Phones"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del teléfono",
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
     *                 @OA\Property(property="phone", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Éxito"),
     *     @OA\Response(response="404", description="No se encontró el teléfono del usuario"),
     *     @OA\Response(response="422", description="Teléfono no válido")
     * )
     */

    public function update(PhoneRequest $request, $id)
    {
        $user = auth()->user();
        $phone = $user->phone;

        if (!$phone) {
            return response()->json(['message' => 'No se encontró el teléfono del usuario'], 404);
        }

        $phone->phone = $request->input('phone');
        $phone->save();

        return new PhoneResource($phone);
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/v1/phones/{id}",
     *     summary="Eliminar teléfono",
     *     tags={"Phones"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del teléfono",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(response="204", description="Teléfono eliminado exitosamente"),
     *     @OA\Response(response="404", description="Teléfono no encontrado")
     * )
     */
    public function destroy(Phone $phone)
    {
        $phone->delete();

        return response()->json([
            'message' => 'Teléfono eliminado exitosamente'
        ], 204);
    }
}
