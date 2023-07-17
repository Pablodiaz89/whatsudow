<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ReplyToMessageRequest;
use App\Http\Requests\MarkMessageAsReadRequest;

/**
 * @OA\Tag(
 *     name="Messages",
 *     description="API Endpoints para manejar las solitudes y almacenaje de los mensajes de texto"
 * )
 */


class MessageController extends Controller
{

    /**
     * Marcar un mensaje como leído.
     *
     * @OA\Put(
     *     path="/api/v1/messages/{id}/mark-as-read",
     *     summary="Marcar un mensaje como leído",
     *     tags={"Messages"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID Mensaje",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="property1", type="string", example="value1"),
     *             @OA\Property(property="property2", type="string", example="value2"),
     *         )
     *     ),
     *     @OA\Response(response="200", description="Mensaje marcado como leído"),
     *     @OA\Response(response="401", description="No autenticado")
     * )
     */

    public function markAsRead(MarkMessageAsReadRequest $request, string $id)
    {
        $message = Message::findOrFail($id);

        // verifica si el usuario autenticado es el destinatario del mensaje
        if ($message->addresse_id !== Auth::id()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $message->read = true;
        $message->save();

        return response()->json(['message' => 'Mensaje marcado como leído']);
    }


    /**
     * Obtener el estado de lectura de un mensaje.
     *
     * @OA\Get(
     *     path="/api/v1/messages/{id}/read-status",
     *     summary="Obtener el estado de lectura de un mensaje",
     *     tags={"Messages"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID Mensaje",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Éxito"),
     *     @OA\Response(response="401", description="No autenticado")
     * )
     */

    public function getReadStatus(string $id)
    {
        $message = Message::findOrFail($id);

        // verifica si el usuario autenticado es el destinatario del mensaje
        if ($message->addresse_id !== Auth::id()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        return response()->json(['read' => $message->read]);
    }

    /**
     * Responder a un mensaje.
     *
     * @OA\Post(
     *     path="/api/v1/messages/{id}/reply",
     *     summary="Responder a un mensaje",
     *     tags={"Messages"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID Mensaje",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="message",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="201", description="Mensaje enviado correctamente"),
     *     @OA\Response(response="401", description="No autenticado")
     * )
     */

    public function reply(ReplyToMessageRequest $request, string $id)
    {
        $message = Message::findOrFail($id);

        // verifica de que el usuario autenticado es el destinatario del mensaje
        if ($message->addressee_id !== Auth::id()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // obtiene el primer mensaje relacionado con el formulario
        $firstMessage = Message::where('parent_id', $message->parent_id)
            ->whereNotNull('title')
            ->first();

        // si el primer mensaje existe, establecer el ID del mensaje padre para el nuevo mensaje.
        // de lo contrario, el mensaje padre será el propio mensaje al que se está respondiendo.
        $parentMessageId = $firstMessage ? $firstMessage->id : $message->parent_id;

        $reply = Message::create([
            'sender_id' => Auth::id(),
            'addressee_id' => $message->sender_id, // el remitente original se convierte en destinatario de la respuesta
            'parent_id' => $parentMessageId,
            'message' => $request->input('message'),
            'read' => false,
        ]);

        return response()->json([
            'message' => 'Mensaje enviado correctamente',
            'data' => $reply,
        ], 201);
    }
}
