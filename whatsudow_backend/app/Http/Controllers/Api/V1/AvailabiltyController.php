<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Availability;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class AvailabiltyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() // obtener las disponibilidades del proveedor actual
    {
        $availabilities = Availability::where('user_id', Auth::id())->get();

        return response()->json($availabilities);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) // almacena la disponibilidad del proveedor
    {
        // validación de los datos de entrada
        $request->validate([
            'title' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:disponible,pre-reservado,no-disponible',
        ]);

        // verificar si es proveedor
        if (!Auth::user()->hasRole('proveedor')) {
            return response()->json(['message' => 'No tienes permiso para realizar esta acción'], 403);
        }

        // crear disponibilidad
        $availability = new Availability();
        $availability->user_id = Auth::id();
        $availability->title = $request->input('title');
        $availability->start_date = $request->input('start_date');
        $availability->end_date = $request->input('end_date');
        $availability->status = $request->input('status');
        $availability->save();

        return response()->json($availability, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // obtener disponibilidad especifica del proveedor que vemos
        $availability = Availability::where('user_id', Auth::id())->find($id);

        if (!$availability) {
            return response()->json(['message' => 'No se encontró la disponibilidad'], 404);
        }

        return response()->json($availability);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) // actualizar disponibilidad del proveedor
    {
        // validación de datos
        $request->validate([
            'title' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:disponible,pre-reservado,no-disponible',
        ]);

        // verificación si es proveedor
        if (!Auth::user()->hasRole('proveedor')) {
            return response()->json(['message' => 'No tienes permiso para realizar esta acción'], 403);
        }

        // obtener disponibilidad
        $availability = Availability::where('user_id', Auth::id())->find($id);
        if (!$availability) {
            return response()->json(['message' => 'No se encontró la disponibilidad'], 404);
        }

        // abcualizar disponibilidad
        $availability->title = $request->input('title');
        $availability->start_date = $request->input('start_date');
        $availability->end_date = $request->input('end_date');
        $availability->status = $request->input('status');
        $availability->save();

        return response()->json($availability);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) // elimina la disponibilidad del proveedor
    {
        // verificar si es proveedor
        if (!Auth::user()->hasRole('proveedor')) {
            return response()->json(['message' => 'No tienes permiso para realizar esta acción'], 403);
        }

        // verificar disponibilidad y si es proveedor
        $availability = Availability::where('user_id', Auth::id())->find($id);
        if (!$availability) {
            return response()->json(['message' => 'No se encontró la disponibilidad'], 404);
        }

        // eliminar disponibilidad
        $availability->delete();

        return response()->json(['message' => 'Disponibilidad eliminada exitosamente']);
    }


    
    public function getEvents() // obtiene los eventos de disponibilidad del proveedor
    {
       // verificar si es proveedor o organizador
       if (Auth::user()->hasAnyRole(['proveedor', 'organizador'])) {
        
        $providers = Role::where('name', 'proveedor')->first()->users; // obtener proveedor y disponibilidad

        // eventos disponibles proveedor
        $events = [];
        foreach ($providers as $provider) {
            $availabilities = Availability::where('user_id', $provider->id)->get();

            foreach ($availabilities as $availability) {
                $start = Carbon::parse($availability->start_date);
                $end = Carbon::parse($availability->end_date);

                // definir el color de disponibilidad
                $color = $availability->status === 'disponible' ? 'green' : ($availability->status === 'pre-reservado' ? 'yellow' : 'red');

                $event = [
                    'title' => $availability->title,
                    'start' => $start->toDateTimeString(),
                    'end' => $end->toDateTimeString(),
                    'color' => $color,
                    'proveedor' => $provider->name, 
                ];

                $events[] = $event;
            }
        }

        return response()->json($events);
        } else {
            // si no es proveedor o organizador
            return response()->json(['message' => 'No tienes permiso para acceder a esta información'], 403);
        }
    }
}
