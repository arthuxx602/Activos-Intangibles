<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'Correo' => ['required', 'string', 'email'],
            'Contraseña' => ['required', 'string', 'min:3'],
        ];
    }

    public function messages(): array
    {
        return [
            'Correo.required' => 'El correo es obligatorio.',
            'Correo.email' => 'Debe ingresar un correo válido.',
            'Contraseña.required' => 'La contraseña es obligatoria.',
            'Contraseña.min' => 'La contraseña debe tener al menos 3 caracteres.',
        ];
    }

    public function authenticate(): void
    {
        if (!Auth::attempt(['Correo' => $this->Correo, 'password' => $this->Contraseña])) {
            $this->throwValidationException($this, [
                'Correo' => __('Estas credenciales no coinciden con nuestros registros.'),
            ]);
        }
    }
}