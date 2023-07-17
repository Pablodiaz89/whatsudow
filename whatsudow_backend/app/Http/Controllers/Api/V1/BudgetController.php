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

/**
 * @OA\Tag(
 *     name="Budgets",
 *     description="Endpoints para las solicitudes relacionadas con los presupuestos"
 * )
 */


class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \App\Http\Resources\V1\BudgetCollection
     *
     * @OA\Get(
     *     path="/budgets",
     *     operationId="getBudgets",
     *     tags={"Budgets"},
     *     summary="Muestra los presupuestos especificos",
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     ref="#/components/schemas/BudgetResource"
     *                 )
     *             )
     *         )
     *     )
     * )
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/budgets",
     *     operationId="storeBudget",
     *     tags={"Budgets"},
     *     summary="Create a new budget",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Almacena un nuevo presupuesto de un usuario concreto",
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="event_date", type="string", format="date"),
     *             @OA\Property(property="location", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Presupuesto creado correctamente",
     *         @OA\JsonContent(
     *             @OA\Schema(
     *                 @OA\Property(property="message", type="string", example="Mensaje enviado correctamente"),
     *                 @OA\Property(property="budget", ref="#/components/schemas/BudgetResource")
     *             )
     *         )
     *     )
     * )
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
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/budgets/{id}",
     *     operationId="showBudget",
     *     tags={"Budgets"},
     *     summary="Obtener detalles de un presupuesto específico",
     *     @OA\Parameter(
     *         name="id",
     *         description="ID de presupuesto",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="budget_title", type="string"),
     *             @OA\Property(property="sender_name", type="string"),
     *             @OA\Property(property="received_date", type="string", format="date"),
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="sent_date", type="string", format="date"),
     *             @OA\Property(
     *                 property="message",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="event_date", type="string", format="date"),
     *                 @OA\Property(property="location", type="string"),
     *                 @OA\Property(property="description", type="string")
     *             )
     *         )
     *     )
     * )
     */

    public function show(string $id)
    {
        $message = Message::findOrFail($id);

        // verifica si el usuario autenticado es el remitente o destinatario del mensaje
        if ($message->sender_id !== Auth::id() && $message->addresse_id !== Auth::id()) {
            return response()->json(['message' => 'No autenticado'], 401);
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
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/budgets/{id}",
     *     operationId="destroyBudget",
     *     tags={"Budgets"},
     *     summary="Eliminar un presupuesto específico",
     *     @OA\Parameter(
     *         name="id",
     *         description="ID de presupuesto",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Presupuesto eliminado con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Mensaje eliminado correctamente")
     *         )
     *     )
     * )
     */

    public function destroy(string $id) // elimina un presupuesto específico
    {
        $message = Message::findOrFail($id);

        // verifica si el usuario autenticado es el remitente del mensaje
        if ($message->sender_id !== Auth::id()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $message->delete();

        return response()->json(['message' => 'Mensaje eliminado correctamente']);
    }
}
