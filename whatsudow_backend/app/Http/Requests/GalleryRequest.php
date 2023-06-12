<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GalleryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:25000', 
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.string' => 'El campo nombre debe ser una cadena de texto.',
            'name.max' => 'El campo nombre no debe exceder los 255 caracteres.',
            'images.required' => 'Debe seleccionar al menos una imagen.',
            'images.array' => 'El campo imágenes debe ser un arreglo.',
            'images.*.image' => 'El archivo debe ser una imagen válida.',
            'images.*.mimes' => 'El archivo debe tener un formato válido (jpeg, png, jpg, gif).',
            'images.*.max' => 'El tamaño máximo permitido para las imágenes es de 25 MB.',
        ];
    }
}
