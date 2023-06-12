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
use App\Http\Resources\V1\BudgetResource;
use App\Http\Requests\CreateBudgetRequest;
use App\Http\Resources\V1\BudgetCollection;

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

        return new BudgetCollection($budgets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateBudgetRequest $request)
    {

        // obtiene los datos del usuario emisor
        $sender = Auth::user();
        $senderName = $sender->name;
        $senderEmail = $sender->email;

        // obtiene el teléfono del usuario emisor
        $senderPhone = Phone::where('user_id', $sender->id)->value('phone');

        // obtiene los datos del usuario destinatario
        $addressee = User::findOrFail($request->input('user_id'));

        // busca la localización existente en la tabla localizaciones
        $location = Location::where('name', $request->input('location'))->first();

        // si la localización no existe, la crea
        if (!$location) {
            $location = new Location();
            $location->name = $request->input('location');
            $location->save();
        }

        // crea y almacena el mensaje
        $message = Message::create([
            'sender_id' => $sender->id,
            'sender_name' => $senderName,
            'sender_email' => $senderEmail,
            'sender_telefono' => $senderPhone,
            'addresse_id' => $addressee->id,
            'title' => $request->input('title'),
            'event_date' => Carbon::createFromFormat('d-m-Y', $request->input('event_date'))->format('Y-m-d'),
            'location_id' => $location->id,
            'description' => $request->input('description'),
            'read' => false,
        ]);

        $message->save();

        return response()->json([
            'message' => 'Mensaje enviado correctamente',
            'data' => new BudgetResource($message),
        ], 201);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $message = Message::findOrFail($id);

        // verifica si el usuario autenticado es el remitente o destinatario del mensaje
        if ($message->sender_id !== Auth::id() && $message->addresse_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        // obtiene título de la solicitud de presupuesto
        $budgetTitle = $message->title;

        // obtiene el nombre de usuario del emisor del mensaje
        $senderName = $message->sender_name;

        // obtiene la fecha de recepción del mensaje
        $receivedDate = $message->created_at->format('d-m-Y');

        // estado del mensaje
        $status = $message->reply_sent ? 'Enviado' : 'Sin responder';

        // obtiene la fecha de envío en caso de que el mensaje haya sido enviado
        $sentDate = $message->reply_sent ? $message->updated_at->format('d-m-Y') : null;

        // pasa los datos a la vista
        return response()->json([
            'budget_title' => $budgetTitle,
            'sender_name' => $senderName,
            'received_date' => $receivedDate,
            'status' => $status,
            'sent_date' => $sentDate,
            'message' => new BudgetResource($message),
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

        // verifica si el usuario autenticado es el remitente del mensaje
        if ($message->sender_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $message->delete();

        return response()->json(['message' => 'Mensaje eliminado correctamente']);
    }

}

    
    

