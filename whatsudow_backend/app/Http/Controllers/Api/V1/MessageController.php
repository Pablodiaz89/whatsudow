<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
                                                     // marcar como leído
    public function markAsRead(Request $request, string $id)
    {
        $message = Message::findOrFail($id);

        // verificación si el usuario autenticado es el destinatario del mensaje
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

        // verificacion si el usuario autenticado es el destinatario del mensaje
        if ($message->addresse_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        return response()->json(['read' => $message->read]);
    }

}
