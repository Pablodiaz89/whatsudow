<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CategoryResquest;
use App\Http\Resources\V1\CategoryResource;
use App\Http\Resources\V1\CategoryCollection;

// controlador para las categorías

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() // muestra las categorías
    {
        $categories = Category::all();
        return new CategoryCollection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryResquest $request) //almacena la una nueva categoría. Si se proporciona un icono lo asocia a la categoría (es opcional)
    {
        // validación
        $data = $request->validated();

        // obtén el ID del usuario autenticado
        $userId = Auth::id();

        // crea la categoría
        $category = new Category();
        $category->name = $data['name'];
        $category->user_id = $userId;

        // asocia el icono a la categoría si se proporciona en la solicitud
        if ($request->has('icon')) {
            $category->icon = $request->input('icon');
        }

        $category->save();

        // respuesta
        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category) // muestra los detalles de la categoría especifica
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryResquest $request, Category $category) // actualiza los datos de la categoría especifica. Si se asocia un nuevo icono, lo asocia a la categoria.
    {
        // validación
        $data = $request->validated();

        // actualiza la categoría
        $category->update($data);

        // Asociar el icono a la categoría si se proporciona en la solicitud
        if ($request->has('icon')) {
            $icon = $request->input('icon');
            $category->icon = $icon;
            $category->save();
        }

        // respuesta
        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category) // elimina la categoría
    {
        $category->delete();

        return response()->json([
            'message' => 'Categoría eliminada con éxito'
        ]);
    }
}
