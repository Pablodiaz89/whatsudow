<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginResquest extends FormRequest
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
    public function rules(): array // validaciones
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required'],
        ];
    }

    public function messages()   // mensajes de errores           
    {
        return [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email no es válido',
            'email.exists' => 'Esa cuenta no existe',
            'password' => 'El password es obligatorio'
        ];
    }
}
