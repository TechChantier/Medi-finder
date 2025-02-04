<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalFacilityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'user' => new UserResource($this->whenLoaded('user'))
        ];
    }
}
// 