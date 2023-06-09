<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password as PasswordRules;

class RegisterProviderResquest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array  // validaciones
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string'],
            'password' => ['required', 'confirmed', PasswordRules::min(8)],
        ];
    }

    public function messages()        // mensajes de errores
    {
        return [
            'name.required' => 'El Nombre es obligatorio',
            'email.required' => 'El Email es obligatorio',
            'email.email' => 'El Email no es válido',
            'email.unique' => 'El usuario ya esta registrado',
            'phone.required' => 'El Teléfono es obligatorio',
            //'phone.numeric' => 'El Teléfono solo puede contener números',
            'password' => 'El Password debe de contener al menos 8 caracteres',
            'password.confirmed' => 'Los Passwords no coinciden',
        ];
    }
}