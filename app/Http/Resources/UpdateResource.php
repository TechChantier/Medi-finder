<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            'name' => $this->user->name ?? null,
            'email' => $this->user->email ?? null,
            'user_type' => $this->user->user_type ?? null,
            'whatsapp_number' => $this->user->whatsapp_number ?? null,
            'image' => $this->user->image ?? null,
            'description' => $this->description,
            'emergency_contact' => $this->emergency_contact,
            'google_map_url' => $this->google_map_url,
            'operating_hours' => $this->operating_hours,
            'status' => $this->status,
            'units' => $this->units,
            'address' => $this->address,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
