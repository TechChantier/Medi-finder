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
            'name' => $this->name,
            'email' => $this->email,
            'description' => $this->description,
            'whatsapp_number' => $this->whatsapp_number,
            'operating_hours' => $this->operating_hours,
            'status' => $this->status,
            'units' => $this->units,
            'user_type' => $this->user_type,
            'image' => $this->image,
            'address' => $this->address,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
