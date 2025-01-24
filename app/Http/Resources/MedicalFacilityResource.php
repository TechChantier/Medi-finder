<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalFacilityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'description' => $this->description,
            'operating_hours' => $this->operating_hours,
            'status' => $this->status,
            'units' => UnitResource::collection($this->whenLoaded('units')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user'))
        ];
    }
}
