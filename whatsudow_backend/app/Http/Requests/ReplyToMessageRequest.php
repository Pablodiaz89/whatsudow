<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReplyToMessageRequest extends FormRequest
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
            'message' => ['required', 'string'],
        ];
    }

    public function messages(): array // mensajes de errores
    {
        return [
            'message.required' => 'El campo mensaje es obligatorio.',
            'message.string' => 'El campo mensaje debe ser una cadena de texto.',
        ];
    }
}
