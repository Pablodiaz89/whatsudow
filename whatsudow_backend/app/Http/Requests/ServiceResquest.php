<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceResquest extends FormRequest
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
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'regex:/^\d+(\.\d{0,2})?$/'],
        ];
    }

    public function messages()        // mensajes de errores
    {
        return [
            'name.required' => 'El Nombre es obligatorio',
            'description' => "La descripción es obligatoria",
            'price.required' => "El campo precio es obligatorio",
            'price.numeric' => "El campo precio solo puede contener numeros",
        ];
    }
}
