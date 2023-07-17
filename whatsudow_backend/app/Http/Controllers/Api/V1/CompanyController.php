<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\V1\CompanyResource;
use App\Http\Resources\V1\CompanyCollection;

/**
 * @OA\Tag(
 *     name="Companies",
 *     description="Endpoints para los nombres de las empresas o compañias empresariales"
 * )
 */


class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     *     path="/companies",
     *     operationId="getCompanies",
     *     tags={"Companies"},
     *     summary="Obtener todas las empresas",
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *             )
     *         )
     *     )
     * )
     */

    public function index()
    {
        $companies = Company::all();
        return new CompanyCollection($companies);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CompanyRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @OA\Post(
     *     path="/companies",
     *     operationId="storeCompany",
     *     tags={"Companies"},
     *     summary="Almacenar una nueva empresa",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empresa creada con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *         )
     *     )
     * )
     */

    public function store(CompanyRequest $request)
    {
        $user = Auth::user();

        $company = new Company();
        $company->name = $request->name;
        $company->user_id = $user->id;
        $company->save();

        return response()->json(new CompanyResource($company), 201);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     *     path="/companies/{company}",
     *     operationId="showCompany",
     *     tags={"Companies"},
     *     summary="Obtener una empresa específica",
     *     @OA\Parameter(
     *         name="company",
     *         description="ID de la compañía",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent()
     *     )
     * )
     */

    public function show(Company $company)
    {
        return new CompanyResource($company);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     *
     * @OA\Put(
     *     path="/companies/{company}",
     *     operationId="updateCompany",
     *     tags={"Companies"},
     *     summary="Actualizar una empresa específica",
     *     @OA\Parameter(
     *         name="company",
     *         description="ID de la compañía",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Datos de la compañia",
     *         required=true,
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa"
     *     )
     * )
     */

    public function update(CompanyRequest $request, Company $company)
    {
        $user = Auth::user();

        $company->name = $request->name;
        $company->save();

        return new CompanyResource($company);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     *
     * @OA\Delete(
     *     path="/companies/{company}",
     *     operationId="destroyCompany",
     *     tags={"Companies"},
     *     summary="Eliminar una empresa específica",
     *     @OA\Parameter(
     *         name="company",
     *         description="ID de la compañía",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa eliminada con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */

    public function destroy(Company $company)
    {
        $company->delete();

        return response()->json([
            'message' => 'Empresa eliminada con éxito'
        ]);
    }
}
