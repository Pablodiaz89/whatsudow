<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ReplyToMessageRequest;
use App\Http\Requests\MarkMessageAsReadRequest;

class MessageController extends Controller
{
                                                     // marcar como leído
    public function markAsRead(MarkMessageAsReadRequest $request, string $id)
    {
        $message = Message::findOrFail($id);

        // verifica si el usuario autenticado es el destinatario del mensaje
        if ($message->addresse_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $message->read = true;
        $message->save();

        return response()->json(['message' => 'Mensaje marcado como leído']);
    }

    /**
     * Obtener el estado de lectura de un mensaje.
     */
    public function getReadStatus(string $id)
    {
        $message = Message::findOrFail($id);

        // verifica si el usuario autenticado es el destinatario del mensaje
        if ($message->addresse_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        return response()->json(['read' => $message->read]);
    }

    public function reply(ReplyToMessageRequest $request, string $id)
    {
        $message = Message::findOrFail($id);

        // verifica de que el usuario autenticado es el destinatario del mensaje
        if ($message->addressee_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 401);
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
