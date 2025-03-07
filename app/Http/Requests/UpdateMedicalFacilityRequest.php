<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMedicalFacilityRequest extends FormRequest
{
    /**
     * Determine if user is authorized to make this request
     */
    public function authorize(): bool
    {
        return $this->user()->isMedicalFacility() &&
            $this->user()->medicalFacility()->exists();
        // return true;
    }
    /**
     * Get validation rules for the request
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'min:4'],
            'email' => ['sometimes', 'required', 'string', 'email', 'unique:users,email'],
            'password' => ['sometimes', 'required', 'string', 'min:6', 'confirmed'],
            'user_type' => ['sometimes', 'required', 'string', 'in:finder,medical_facility'],
            'whatsapp_number' => ['sometimes', 'required', 'string', 'max:12',],
            'image' => ['nullable', 'image'],



            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'emergency_contact' => ['sometimes', 'string', 'max:12',],
            'google_map_url' => ['sometimes', 'string'],
            'operating_hours' => ['sometimes', 'required', 'array'],
            'operating_hours.*.day' => [
                'required',
                Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])
            ],
            'operating_hours.*.open' => ['required', 'date_format:H:i'],
            'operating_hours.*.close' => ['required', 'date_format:H:i'],
            'status' => [
                'sometimes',
                'required',
                Rule::in(['active', 'inactive', 'pending'])
            ],
            'category' => [
                'sometimes',
                'required',
                Rule::in(['Health Care', 'Pharmacy', 'Clinic'])
            ],
            'units' => ['sometimes', 'required', 'array'],
            'units.*' => ['exists:units,id']
        ];
    }
    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'address.required' => 'The facility address is required',
            'emergency_contact' => 'Emergency contact must be atleast 2 digits at most 6 digits',
            'operating_hours.*.day.in' => 'Invalid day selected',
            'operating_hours.*.open.date_format' => 'Opening time must be in 24-hour format (HH:mm)',
            'operating_hours.*.close.date_format' => 'Closing time must be in 24-hour format (HH:mm)',
            'units.*.exists' => 'One or more selected units are invalid'
        ];
    }
}
