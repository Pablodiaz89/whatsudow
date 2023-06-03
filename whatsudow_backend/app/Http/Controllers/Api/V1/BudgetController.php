<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Phone;
use App\Models\Budget;
use App\Models\Message;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $budgets = Budget::all()->map(function ($budget) {
            $budget->event_date = Carbon::parse($budget->event_date)->format('d-m-Y');
            return $budget;
        });
    
        return response()->json($budgets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validación
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required',
            'event_date' => 'required|date',
            'location' => 'required|string',
            'description' => 'required',
            'message' => 'required', 
        ]);

        // obtencion de los datos del usuario emisor
        $sender = Auth::user();
        $senderName = $sender->name;
        $senderEmail = $sender->email;

        // obtencion del teléfono del usuario emisor
        $senderPhone = Phone::where('user_id', $sender->id)->value('phone');

        // obtención de los datos del usuario destinatario
        $addressee = User::findOrFail($request->input('user_id'));

        // busqueda de la localización existente en la tabla localizaciones
        $location = Location::where('name', $request->input('location'))->first();

        // si la localización no existe, crearla
        if (!$location) {
            $location = new Location();
            $location->name = $request->input('location');
            $location->save();
        }    

        // creacion y almacenamiento del mensaje
        $message = Message::create([
            'sender_id' => $sender->id,
            'sender_name' => $senderName,
            'sender_email' => $senderEmail,
            'sender_telefono' => $senderPhone,
            'addresse_id' => $addressee->id,
            'title' => $request->input('title'),
            'event_date' => Carbon::parse($request->input('event_date'))->format('d-m-Y'),
            'location_id' => $location->id,
            'description' => $request->input('description'),
            'message' => $request->input('message'),
            'read' => false,
        ]);

        $message->save();

        return response()->json([
            'message' => 'Mensaje enviado correctamente',
            'data' => $message,
        ], 201);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $message = Message::findOrFail($id);

        // verificacion si el usuario autenticado es el remitente o destinatario del mensaje
        if ($message->sender_id !== Auth::id() && $message->addresse_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        // obtención del título de la solicitud de presupuesto
        $budgetTitle = $message->title;

        // obtencion del nombre de usuario del emisor del mensaje
        $senderName = $message->sender_name;

        // obtencion de la fecha de recepción del mensaje
        $receivedDate = $message->created_at->format('d-m-Y');

        // estado del mensaje
        $status = $message->reply_sent ? 'Enviado' : 'Sin responder';

        // obtencion de la fecha de envío en caso de que el mensaje haya sido enviado
        $sentDate = $message->reply_sent ? $message->updated_at->format('d-m-Y') : null;

        // Pasar los datos a la vista
        return response()->json([
            'budget_title' => $budgetTitle,
            'sender_name' => $senderName,
            'received_date' => $receivedDate,
            'status' => $status,
            'sent_date' => $sentDate,
            'message' => $message,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = Message::findOrFail($id);

        // verificacion si el usuario autenticado es el remitente del mensaje
        if ($message->sender_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $message->delete();

        return response()->json(['message' => 'Mensaje eliminado correctamente']);

    }



    public function reply(Request $request, string $id)
    {
        $message = Message::findOrFail($id);

        // verificación del usuario autenticado es el destinatario del mensaje
        if ($message->addresse_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        // obtencion de los datos del usuario emisor original (quien realizó la solicitud de presupuesto)
        $sender = $message->sender;
        $senderName = $sender->name;
        $senderEmail = $sender->email;

        // obtencion del teléfono del usuario emisor original
        $senderPhone = $sender->phone->phone;

        // creacion y almacenamiento del mensaje de respuesta
        $reply = Message::create([
            'sender_id' => $sender->id,
            'sender_name' => $senderName,
            'sender_email' => $senderEmail,
            'sender_telefono' => $senderPhone,
            'addresse_id' => $message->sender_id, // remitente original se convierte en destinatario de la respuesta
            'parent_id' => $message->id, // id del mensaje padre
            'title' => $message->title, // título del mensaje original
            'event_date' => $message->event_date, // fecha del evento del mensaje original
            'location_id' => $message->location_id, // ubicación del mensaje original
            'description' => $message->description, // descripción del mensaje original
            'message' => $request->input('message'),
            'read' => false,
        ]);

        return response()->json([
            'message' => 'Mensaje enviado correctamente',
            'data' => $reply,
        ], 201);
    }
}

    
    

