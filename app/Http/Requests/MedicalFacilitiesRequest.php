<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicalFacilitiesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'address' => ['required', 'string'],
            'category' => ['required', 'in:Health Care,Pharmacy,Clinic'],
            'description' => ['required', 'string'],
            'emergency_contact' => ['nullable', 'string', 'max:10'],
            'google_map_url' => ['nullable', 'string'],
            'operating_hours' => ['nullable', 'string'],
            'status' => ['required', 'in:Open,Closed'],
            'units' => ['required', 'array', 'max:255'],
            'units.*' => ['string'],
        ];
    }
}
