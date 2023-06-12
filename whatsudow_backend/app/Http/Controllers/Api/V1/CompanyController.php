<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\V1\CompanyResource;
use App\Http\Resources\V1\CompanyCollection;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::all();
        return new CompanyCollection($companies);
    }

    /**
     * Store a newly created resource in storage.
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
     */
    public function show(Company $company)
    {
        return new CompanyResource($company);
    }

    /**
     * Update the specified resource in storage.
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
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return response()->json([
            'message' => 'Empresa eliminada con éxito'
        ]);
    }
}
