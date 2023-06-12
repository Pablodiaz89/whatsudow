<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvailabilityRequest  extends FormRequest
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
    public function rules(): array
    {
        return [
            'title' => 'required',
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y|after:start_date',
            'status' => 'required|in:disponible,pre-reservado,no-disponible',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El campo título es obligatorio.',
            'start_date.required' => 'El campo fecha de inicio es obligatorio.',
            'start_date.date_format' => 'El formato de la fecha de inicio debe ser dd/mm/aaaa.',
            'end_date.required' => 'El campo fecha de finalización es obligatorio.',
            'end_date.date_format' => 'El formato de la fecha de finalización debe ser dd/mm/aaaa.',
            'end_date.after' => 'La fecha de finalización debe ser posterior a la fecha de inicio.',
            'status.required' => 'El campo estado es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
        ];
    }
}
