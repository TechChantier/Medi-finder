<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isMedicalFacility();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:medical_facility_units'],
            'description' => ['required', 'string'],
            'status' => [
                'required',
                Rule::in(['active', 'inactive'])
            ]
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Unit name is required',
            'name.unique' => 'This unit name already exists',
            'description.required' => 'Unit description is required',
            'status.in' => 'Status must be either active or inactive'
        ];
    }
}
