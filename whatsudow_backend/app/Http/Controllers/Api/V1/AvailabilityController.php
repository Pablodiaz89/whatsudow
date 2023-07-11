<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use Google\Client;
use App\Models\User;
use App\Models\Availability;
use Google\Service\Calendar;
use Illuminate\Http\Request;
use Google\Service\Calendar\Event;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

use App\Http\Requests\AvailabilityRequest;
use App\Http\Resources\V1\AvailabilityResource;
use App\Http\Resources\V1\AvailabilityCollection;


// Este controlador es de disponibilidad (calendario - sistema de citas)

class AvailabilityController extends Controller
{
    use HasRoles;

    private $calendarService;

    public function __construct() // inicia el cliente de Google Calendar con las credenciales de la aplicación
    {
        // cliente de Google Calendar
        $client = new Client();
        $client->setApplicationName('whatsudow');
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        $client->addScope(Calendar::CALENDAR_EVENTS);

        $this->calendarService = new Calendar($client);
    }

    public function syncGoogleCalendar() // redirige al usuario a la página de autenticación de Google ara sincronizar el calendario
    {
        $client = new Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        $client->addScope('https://www.googleapis.com/auth/calendar');

        $authUrl = $client->createAuthUrl();

        return redirect()->away($authUrl);
    }

    /**
     * Display a listing of the resource.
     */
    public function index() // obtener las disponibilidades del proveedor actual
    {
        $availabilities = Availability::where('user_id', auth()->id())->get();

        return AvailabilityResource::collection($availabilities);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(AvailabilityRequest $request) // crea una nueva disponibilidad para el proveedor y la almacena en la base de datos. Tambien crea un evento en Google Calendar para esa disponibilidad
    {

        // verificar si es proveedor
        if (!$this->hasRole('proveedor')) {
            return response()->json(['message' => 'No tienes permiso para realizar esta acción'], 403);
        }

        // crear disponibilidad
        $availability = new Availability();
        $availability->user_id = Auth::user()->id;
        $availability->title = $request->input('title');
        $availability->start_date = Carbon::createFromFormat('d/m/Y', $request->input('start_date'));
        $availability->end_date = Carbon::createFromFormat('d/m/Y', $request->input('end_date'));
        $availability->status = $request->input('status');
        $availability->save();

        // crear evento en Google Calendar
        $event = new Event([
            'summary' => $availability->title,
            'start' => [
                'dateTime' => Carbon::parse($availability->start_date)->format('d/m/Y\TH:i:sP'),
                'timeZone' => 'Europe/Madrid',
            ],
            'end' => [
                'dateTime' => Carbon::parse($availability->end_date)->format('d/m/Y\TH:i:sP'),
                'timeZone' => 'Europe/Madrid',
            ],
        ]);

        $calendarId = 'primary'; // Puedes ajustar el ID del calendario según tus necesidades

        $event = $this->calendarService->events->insert($calendarId, $event);

        return new AvailabilityResource($availability);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) // muestra la disponibilidad específica del proveedor
    {
        // obtener disponibilidad especifica del proveedor que vemos
        $availability = Availability::where('user_id', Auth::user()->id)->find($id);

        if (!$availability) {
            return response()->json(['message' => 'No se encontró la disponibilidad'], 404);
        }

        return new AvailabilityResource($availability);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(AvailabilityRequest $request, string $id) // actualiza la disponibilidad especifica del proveedor
    {
        // verificación si es proveedor
        if (!$this->hasRole('proveedor')) {
            return response()->json(['message' => 'No tienes permiso para realizar esta acción'], 403);
        }

        // obtener disponibilidad
        $availability = Availability::where('user_id', Auth::user()->id)->find($id);
        if (!$availability) {
            return response()->json(['message' => 'No se encontró la disponibilidad'], 404);
        }

        // actualizar disponibilidad
        $availability->title = $request->input('title');
        $availability->start_date = Carbon::createFromFormat('d/m/Y', $request->input('start_date'));
        $availability->end_date = Carbon::createFromFormat('d/m/Y', $request->input('end_date'));
        $availability->status = $request->input('status');
        $availability->save();

        return new AvailabilityResource($availability);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) // elimina la disponibilidad del proveedor especifico
    {
        // verificar si es proveedor
        if (!$this->hasRole('proveedor')) {
            return response()->json(['message' => 'No tienes permiso para realizar esta acción'], 403);
        }

        // verificar disponibilidad y si es proveedor
        $availability = Availability::where('user_id', Auth::user()->id)->find($id);
        if (!$availability) {
            return response()->json(['message' => 'No se encontró la disponibilidad'], 404);
        }

        // eliminar disponibilidad
        $availability->delete();

        return response()->json(['message' => 'Disponibilidad eliminada exitosamente']);
    }



    public function getEvents() // obtiene los eventos de disponibilidad del proveedor. Cada evento se colorea de acuerdo con el estado de la disponibilidad: verde para disponible, amarillo para pre-reservado y rojo para no disponible.
    {
        // verificar si es proveedor o organizador
        if ($this->hasAnyRole(['proveedor', 'organizador'])) {
            $providers = Role::where('name', 'proveedor')->first()->users; // obtener proveedor y disponibilidad

            // eventos disponibles proveedor
            $events = [];
            foreach ($providers as $provider) {
                $availabilities = Availability::where('user_id', $provider->id)->get();

                foreach ($availabilities as $availability) {
                    $start = Carbon::parse($availability->start_date)->format('d/m/Y');
                    $end = Carbon::parse($availability->end_date)->format('d/m/Y');

                    // definir el color de disponibilidad
                    $color = $availability->status === 'disponible' ? 'green' : ($availability->status === 'pre-reservado' ? 'yellow' : 'red');

                    $event = [
                        'title' => $availability->title,
                        'start' => $start,
                        'end' => $end,
                        'color' => $color,
                        'proveedor' => $provider->name,
                    ];

                    $events[] = $event;
                }
            }

            return response()->json($events);
        }

        return response()->json(['message' => 'No tienes permiso para realizar esta acción'], 403);
    }
}
