<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DocumentRequest;
use App\Http\Resources\V1\DocumentResource;
use App\Http\Resources\V1\DocumentCollection;

/**
 * @OA\Tag(
 *     name="Documents",
 *     description="Endpoints para documentos de identificación del usuario"
 * )
 */

class DocumentController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     *     path="/documents",
     *     operationId="getDocuments",
     *     tags={"Documents"},
     *     summary="Obtener todos los documentos",
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example="1"),
     *                 @OA\Property(property="user_id", type="integer", example="1"),
     *                 @OA\Property(property="document_indetification", type="string", example="ABC123"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */

    public function index()
    {
        // muestra todos los documentos
        return new DocumentCollection(Document::all());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\DocumentRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @OA\Post(
     *     path="/documents",
     *     operationId="storeDocument",
     *     tags={"Documents"},
     *     summary="Almacenar un nuevo documento de identidad",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="document_indetification", type="string", example="ABC123"),
     *             @OA\Property(property="user_id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Documento creado con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="user_id", type="integer", example="1"),
     *             @OA\Property(property="document_indetification", type="string", example="ABC123"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     )
     * )
     */

    public function store(DocumentRequest $request)
    {
        // obtiene el usuario autenticado
        $user = Auth::user();

        // crea el documento
        $document = new Document();
        $document->document_indetification = $request->document_indetification;
        $document->user_id = $user->id;
        $document->save();

        // respuesta
        return response()->json(new DocumentResource($document), 201);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     *     path="/documents/{id}",
     *     operationId="showDocument",
     *     tags={"Documents"},
     *     summary="Obtener un documento específico",
     *     @OA\Parameter(
     *         name="id",
     *         description="ID documento",
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
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="user_id", type="integer", example="1"),
     *             @OA\Property(property="document_indetification", type="string", example="ABC123"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     )
     * )
     */

    public function show(Document $document)
    {
        // respuesta
        return new DocumentResource($document);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     *
     * @OA\Put(
     *     path="/documents/{id}",
     *     operationId="updateDocument",
     *     tags={"Documents"},
     *     summary="Actualizar un documento específico",
     *     @OA\Parameter(
     *         name="id",
     *         description="ID documento",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="document_indetification",
     *         description="ID del documento de identidad",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Documento actualizado con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="user_id", type="integer", example="1"),
     *             @OA\Property(property="document_indetification", type="string", example="ABC123"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     )
     * )
     */

    public function update(DocumentRequest $request, Document $document)
    {
        // validación
        $data = $request->validated();

        // actualiza el documento
        $document->update($data);

        // respuesta
        return new DocumentResource($document);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     *
     * @OA\Delete(
     *     path="/documents/{id}",
     *     operationId="deleteDocument",
     *     tags={"Documents"},
     *     summary="Eliminar un documento específico",
     *     @OA\Parameter(
     *         name="id",
     *         description="ID documento",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Documento de identidad eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */

    public function destroy(Document $document)
    {
        // elimina el documento
        $document->delete();

        // respuesta
        return response()->json([
            'message' => 'Documento de identidad eliminado exitosamente'
        ]);
    }
}
