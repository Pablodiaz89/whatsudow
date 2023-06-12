<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Phone;
use Illuminate\Http\Request;
use App\Http\Requests\PhoneRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\V1\PhoneResource;

class PhoneController extends Controller
{
    /**
     * Display a listing of the resource.
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
    public function destroy(Phone $phone)
    {
        //
    }
}
