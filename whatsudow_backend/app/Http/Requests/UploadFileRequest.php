<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
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
            'file' => 'required|file|max:25000',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'El campo archivo es obligatorio.',
            'file.file' => 'El campo debe ser un archivo válido.',
            'file.max' => 'El archivo no debe superar los 25 MB.',
        ];
    }
}
