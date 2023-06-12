<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'sender_name' => $this->sender_name,
            'sender_email' => $this->sender_email,
            'sender_telefono' => $this->sender_telefono,
            'addresse_id' => $this->addresse_id,
            'parent_id' => $this->parent_id,
            'title' => $this->title,
            'event_date' => $this->event_date,
            'location_id' => $this->location_id,
            'description' => $this->description,
            'message' => $this->message,
            'read' => $this->read,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
