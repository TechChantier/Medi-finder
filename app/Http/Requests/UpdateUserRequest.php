<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'min:4'],
            'email' => ['sometimes', 'required', 'string', 'email', 'unique:users,email'],
            'password' => ['sometimes', 'required', 'string', 'min:6', 'confirmed'],
            'user_type' => ['sometimes', 'required', 'string', 'in:finder,medical_facility'],
            'whatsapp_number' => ['sometimes', 'required', 'string', 'max:12',],
            'image' => ['nullable', 'image'],
        ];
    }
}
