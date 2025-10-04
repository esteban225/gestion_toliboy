<?php

namespace App\Modules\Forms\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FormFiltersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:100|unique:forms,code',
            'is_active' => 'sometimes|boolean',
            'display_order' => 'sometimes|integer|min:0',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    public function messages()
    {
        return [
            'name.string' => 'El campo nombre debe ser una cadena de texto.',
            'name.max' => 'El campo nombre no debe exceder los 255 caracteres.',
            'code.string' => 'El campo código debe ser una cadena de texto.',
            'code.max' => 'El campo código no debe exceder los 100 caracteres.',
            'code.unique' => 'El código ya está en uso. Por favor, elige otro.',
            'is_active.boolean' => 'El campo activo debe ser verdadero o falso.',
            'display_order.integer' => 'El campo orden de visualización debe ser un número entero.',
            'display_order.min' => 'El campo orden de visualización no puede ser negativo.',
        ];
    }
}
