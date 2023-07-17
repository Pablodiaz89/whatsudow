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

/**
 * @OA\Tag(
 *     name="Availability",
 *     description="Endpoints para disponibilidad (calendario - sistema de citas)"
 * )
 */


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

    /**
     * Sincronizar con Google Calendar.
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @OA\Get(
     *     path="/availability/sync-google-calendar",
     *     operationId="syncGoogleCalendar",
     *     summary="Sincronizacion con Google Calendar",
     *     description="Redirige al usuario a la página de autenticación de Google para sincronizar el calendario.",
     *     tags={"Availability"},
     *     @OA\Response(
     *         response=302,
     *         description="Redirigir a la página de autenticación de Google"
     *     )
     * )
     */

    public function syncGoogleCalendar()
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
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @OA\Get(
     *     path="/availability",
     *     operationId="getAvailabilities",
     *     summary="Obtiene las disponibilidades del proveedor",
     *     description="Recupera las disponibilidades del proveedor.",
     *     tags={"Availability"},
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="start_date", type="string", format="date"),
     *                 @OA\Property(property="end_date", type="string", format="date"),
     *                 @OA\Property(property="status", type="string", enum={"disponible", "pre-reservado", "no-disponible"})
     *             )
     *         )
     *     )
     * )
     */


    public function index()
    {
        $availabilities = Availability::where('user_id', auth()->id())->get();

        return AvailabilityResource::collection($availabilities);
    }


    /**
     * @OA\Post(
     *     path="/availability",
     *     operationId="createAvailability",
     *     summary="Crea una nueva disponibilidad",
     *     description="Crea una nueva disponibilidad para el proveedor y la almacena en la base de datos. También crea un evento en Google Calendar para esa disponibilidad.",
     *     tags={"Availability"},
     *     @OA\RequestBody(
     *         description="Datos de disponibilidad",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="start_date", type="string", format="date"),
     *             @OA\Property(property="end_date", type="string", format="date"),
     *             @OA\Property(property="status", type="string", enum={"disponible", "pre-reservado", "no-disponible"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Disponibilidad creada con éxito"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No tienes permiso para realizar esta acción"
     *     )
     * )
     */

    public function store(AvailabilityRequest $request)
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

        $calendarId = 'primary';

        $event = $this->calendarService->events->insert($calendarId, $event);

        return new AvailabilityResource($availability);
    }

    /**
     * @OA\Get(
     *     path="/availability/{id}",
     *     operationId="getAvailability",
     *     summary="Obtener la disponibilidad especificada",
     *     description="Recupera la disponibilidad especificada para el proveedor.",
     *     tags={"Availability"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la disponibilidad",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="start_date", type="string", format="date"),
     *             @OA\Property(property="end_date", type="string", format="date"),
     *             @OA\Property(property="status", type="string", enum={"disponible", "pre-reservado", "no-disponible"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontró la disponibilidad"
     *     )
     * )
     */

    public function show(string $id)
    {
        // obtener disponibilidad especifica del proveedor que vemos
        $availability = Availability::where('user_id', Auth::user()->id)->find($id);

        if (!$availability) {
            return response()->json(['message' => 'No se encontró la disponibilidad'], 404);
        }

        return new AvailabilityResource($availability);
    }


    /**
     * Actualización de una disponibilidad especificada.
     *
     * @param \App\Http\Requests\AvailabilityRequest $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/availability/{id}",
     *     operationId="updateAvailability",
     *     summary="Actualizar la disponibilidad especificada",
     *     description="Actualiza la disponibilidad especificada para el proveedor.",
     *     tags={"Availability"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la disponibilidad",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Datos de disponibilidad",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="start_date", type="string", format="date"),
     *             @OA\Property(property="end_date", type="string", format="date"),
     *             @OA\Property(property="status", type="string", enum={"disponible", "pre-reservado", "no-disponible"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Disponibilidad actualizada con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="start_date", type="string", format="date"),
     *             @OA\Property(property="end_date", type="string", format="date"),
     *             @OA\Property(property="status", type="string", enum={"disponible", "pre-reservado", "no-disponible"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No tienes permiso para realizar esta acción",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontró la disponibilidad",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */

    public function update(AvailabilityRequest $request, string $id)
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
     * Elimina la disponibilidad especificada.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/availability/{id}",
     *     operationId="deleteAvailability",
     *     summary="Elimina la disponibilidad especificada",
     *     description="Elimina la disponibilidad especificada para el proveedor.",
     *     tags={"Availability"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la disponibilidad",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Disponibilidad eliminada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No tienes permiso para realizar esta acción",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontró la disponibilidad",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */

    public function destroy(string $id)
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

    /**
     * Obtener los eventos de disponibilidad.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/availability/events",
     *     operationId="getAvailabilityEvents",
     *     summary="Obtener los eventos de disponibilidad",
     *     description="Recupera los eventos de disponibilidad para los proveedores. Cada evento está codificado por colores según el estado de disponibilidad: verde para disponible, amarillo para reserva previa y rojo para no disponible.",
     *     tags={"Availability"},
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="start", type="string", format="date"),
     *                 @OA\Property(property="end", type="string", format="date"),
     *                 @OA\Property(property="color", type="string"),
     *                 @OA\Property(property="proveedor", type="string"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No tienes permiso para realizar esta acción",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */

    public function getEvents()
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
