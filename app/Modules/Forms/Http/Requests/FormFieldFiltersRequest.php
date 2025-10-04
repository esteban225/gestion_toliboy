<?php

namespace App\Modules\Forms\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FormFieldFiltersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    public function messages()
    {
        return [
            'label.string' => 'El campo etiqueta debe ser una cadena de texto.',
            'label.max' => 'El campo etiqueta no debe exceder los 255 caracteres.',
            'field_code.string' => 'El campo código de campo debe ser una cadena de texto.',
            'field_code.max' => 'El campo código de campo no debe exceder los 100 caracteres.',
            'field_code.exists' => 'El código de campo especificado no existe.',
            'type.string' => 'El campo tipo debe ser una cadena de texto.',
            'type.max' => 'El campo tipo no debe exceder los 50 caracteres.',
            'required.boolean' => 'El campo obligatorio debe ser verdadero o falso.',
            'field_order.integer' => 'El campo orden de campo debe ser un número entero.',
            'field_order.min' => 'El campo orden de campo no puede ser negativo.',
            'field_order.exists' => 'El orden de campo especificado no existe.',
            'is_active.boolean' => 'El campo activo debe ser verdadero o falso.',
        ];
    }
}
