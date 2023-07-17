<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CategoryResquest;
use App\Http\Resources\V1\CategoryResource;
use App\Http\Resources\V1\CategoryCollection;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="Endpoints para las categorías"
 * )
 */


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \App\Http\Resources\V1\CategoryCollection
     *
     * @OA\Get(
     *     path="/categories",
     *     operationId="getCategories",
     *     tags={"Categories"},
     *     summary="Obtener una lista de categorías",
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="icon", type="string"),
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function index()
    {
        $categories = Category::all();
        return new CategoryCollection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @return \App\Http\Resources\V1\CategoryResource
     *
     * @OA\Post(
     *     path="/categories",
     *     operationId="storeCategory",
     *     tags={"Categories"},
     *     summary="Crea una nueva categoría. Si se proporciona un icono lo asocia a la categoría (es opcional)",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="icon", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Categoría creada con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="icon", type="string"),
     *         )
     *     )
     * )
     */

    public function store(CategoryResquest $request)
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
     *
     * @param  \App\Models\Category  $category
     * @return \App\Http\Resources\V1\CategoryResource
     *
     * @OA\Get(
     *     path="/categories/{category}",
     *     operationId="showCategory",
     *     tags={"Categories"},
     *     summary="Obtener detalles de una categoría específica",
     *     @OA\Parameter(
     *         name="category",
     *         description="categoria ID",
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
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="icon", type="string")
     *             )
     *         )
     *     )
     * )
     */

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \App\Http\Resources\V1\CategoryResource
     *
     * @OA\Put(
     *     path="/categories/{category}",
     *     operationId="updateCategory",
     *     tags={"Categories"},
     *     summary="Actualizar los datos de la categoría especifica. Si se asocia un nuevo icono, lo asocia a la categoria.",
     *     @OA\Parameter(
     *         name="category",
     *         description="categoria ID",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="icon", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría actualizada con éxito",
     *         @OA\JsonContent(
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="icon", type="string")
     *             )
     *         )
     *     )
     * )
     */

    public function update(CategoryResquest $request, Category $category)
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
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/categories/{category}",
     *     operationId="destroyCategory",
     *     tags={"Categories"},
     *     summary="Eliminar una categoría específica",
     *     @OA\Parameter(
     *         name="category",
     *         description="categoria ID",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría eliminada con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Categoría eliminada con éxito")
     *         )
     *     )
     * )
     */

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Categoría eliminada con éxito'
        ]);
    }
}
