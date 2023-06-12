<?php

namespace App\Http\Resources\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
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
            'event_date' => Carbon::parse($this->event_date)->format('d-m-Y'),
            'title' => $this->title,
            'location' => $this->location,
            'description' => $this->description,
            'sender' => [
                'name' => $this->sender_name,
                'email' => $this->sender_email,
                'phone' => $this->sender_phone,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
