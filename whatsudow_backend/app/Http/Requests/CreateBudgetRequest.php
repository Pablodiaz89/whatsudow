<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBudgetRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'event_date' => 'required|date_format:d-m-Y',
            'location' => 'required|string',
            'description' => 'required|string',
        ];
    }

    public function messages(): array // mensajes de errores
    {
        return [
            'user_id.required' => 'El campo ID de usuario es obligatorio.',
            'user_id.exists' => 'El ID de usuario no existe en la base de datos.',
            'title.required' => 'El campo título es obligatorio.',
            'title.string' => 'El campo título debe ser una cadena de texto.',
            'event_date.required' => 'El campo fecha del evento es obligatorio.',
            'event_date.date_format' => 'El formato de la fecha del evento debe ser dd-mm-aaaa.',
            'location.required' => 'El campo ubicación es obligatorio.',
            'location.string' => 'El campo ubicación debe ser una cadena de texto.',
            'description.required' => 'El campo descripción es obligatorio.',
            'description.string' => 'El campo descripción debe ser una cadena de texto.',
        ];
    }
}
