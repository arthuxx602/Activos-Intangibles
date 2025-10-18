<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // permitir a todos
    }

    public function rules(): array
    {
        return [
            'cedula'     => ['required', 'numeric'],
            'contrasena' => ['required', 'string', 'min:3'],
        ];
    }

    public function messages(): array
    {
        return [
            'cedula.required'     => 'La cédula es obligatoria.',
            'cedula.numeric'      => 'La cédula debe ser numérica.',
            'contrasena.required' => 'La contraseña es obligatoria.',
        ];
    }
}
