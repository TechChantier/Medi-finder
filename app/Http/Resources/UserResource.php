<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform user resource into array
     * Returns different data based on user type
     */
    public function toArray(Request $request): array
    {
        // Basic finder user data
        if ($this->user_type === 'finder') {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'whatsapp_number' => $this->whatsapp_number,
                'user_type' => $this->user_type,
                'image' => $this->when($this->image, fn() => $this->image_url)
            ];
        }        // Medical facility user data with facility details
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'whatsapp_number' => $this->whatsapp_number,
            'user_type' => $this->user_type,
            'image' => $this->when($this->image, fn() => $this->image_url),
            'medical_facility' => $this->when($this->medicalFacility, fn() => [
                'id' => $this->medicalFacility->id,
                'address' => $this->medicalFacility->address,
                'category' => $this->medicalFacility->category,
                'emergency_contact' => $this->medicalFacility->emergency_contact,
                'google_map_url' => $this->medicalFacility->google_map_url,
                'description' => $this->medicalFacility->description,
                'operating_hours' => $this->medicalFacility->operating_hours,
                'status' => $this->medicalFacility->status,
                'units' => $this->medicalFacility->units,
                'created_at' => $this->medicalFacility->created_at,
                'updated_at' => $this->medicalFacility->updated_at
            ])
        ];
    }
}
