<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isMedicalFacility();
    }
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'status' => [
                'sometimes',
                'required',
                Rule::in(['active', 'inactive'])
            ]
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Unit name is required',
            'description.required' => 'Unit description is required',
            'status.in' => 'Status must be either active or inactive'
        ];
    }
}
