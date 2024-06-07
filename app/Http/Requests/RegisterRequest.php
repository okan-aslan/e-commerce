<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'min:5', 'string'],
            'email' => ['required', 'email', 'unique:users,email', 'string'],
            'password' => ['required', 'min:8', 'max:255'],
            'device_name' => ['required', 'string', 'min:5', 'max:255'],
        ];
    }
}
