<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PdfRequest extends FormRequest
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
            'file' => 'required|file|max:25000|mimes:pdf',
            'session_id' => 'required|exists:sessions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'El campo archivo es obligatorio.',
            'file.file' => 'El campo archivo debe ser un archivo.',
            'file.max' => 'El tamaño máximo permitido para el archivo es de 25 MB.',
            'file.mimes' => 'El archivo debe tener un formato PDF.',
            'session_id.required' => 'El campo ID de sesión es obligatorio.',
            'session_id.exists' => 'El ID de sesión no existe en la base de datos.',
        ];
    }
}
