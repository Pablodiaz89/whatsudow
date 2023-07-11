<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvatarRequest extends FormRequest
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
            'file' => 'nullable|image|mimes:jpeg,png,jpg|max:25000',
        ];
    }

    public function messages(): array // mensajes de errores
    {
        return [
            'file.required' => 'El campo archivo es obligatorio.',
            'file.image' => 'El archivo debe ser una imagen.',
            'file.mimes' => 'El archivo debe tener uno de los siguientes formatos: jpeg, png, jpg.',
            'file.max' => 'El tamaño máximo del archivo debe ser 25mb.',
        ];
    }
}
