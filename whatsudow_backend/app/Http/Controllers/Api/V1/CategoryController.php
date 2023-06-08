<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryResquest;
use App\Http\Resources\V1\CategoryCollection;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new CategoryCollection(Category::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryResquest $request)
    {
        // validación
        $data = $request->validated();
        
        // crear
        $category = Category::create($data);
        
        /*
        // asociación del icono a la categoría
        $category->icono = 'nombre-del-icono.png'; 
        $category->save(); 
        */

        // respuesta
        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
       // respuesta
       return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryResquest $request, Category $category)
    {
        // validación
        $data = $request->validated();

        // actualización
        $category->update($data);

        /*
        // asociación del icono a la categoría
        $category->icono = 'nombre-del-icono.png'; 
        $category->save(); 
        */

        // respuesta
        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
