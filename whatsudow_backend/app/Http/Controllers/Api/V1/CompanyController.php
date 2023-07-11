<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\V1\CompanyResource;
use App\Http\Resources\V1\CompanyCollection;

// este controlador maneja los nombres de las empresas o compañias empresariales

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() // retorna la empresa
    {
        $companies = Company::all();
        return new CompanyCollection($companies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request) // almacena la empresa del usuario
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
     */
    public function show(Company $company) // muestra la empresa
    {
        return new CompanyResource($company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, Company $company) // actualiza el nombre de la empresa
    {
        $user = Auth::user();

        $company->name = $request->name;
        $company->save();

        return new CompanyResource($company);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company) // elimina el nombre de la empresa
    {
        $company->delete();

        return response()->json([
            'message' => 'Empresa eliminada con éxito'
        ]);
    }
}
