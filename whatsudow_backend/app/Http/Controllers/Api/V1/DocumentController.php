<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DocumentRequest;
use App\Http\Resources\V1\DocumentResource;
use App\Http\Resources\V1\DocumentCollection;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // muestra todos los documentos
        return new DocumentCollection(Document::all());
    }

    /**
     * Store a newly created resource in storage.
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
     */
    public function show(Document $document)
    {
        // respuesta
        return new DocumentResource($document);
    }

    /**
     * Update the specified resource in storage.
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
     */
    public function destroy(Document $document)
    {
        // elimina el documento
        $document->delete();
        
        // respuesta
        return response()->json([
            'message' => 'Documento eliminado exitosamente'
        ]);
    }
}
