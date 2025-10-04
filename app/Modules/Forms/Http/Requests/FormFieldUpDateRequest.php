<?php

namespace App\Modules\Forms\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FormFieldUpDateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'label' => 'sometimes|string|max:255',
            'field_code' => 'sometimes|string|max:100',
            'type' => 'sometimes|string|max:50',
            'required' => 'sometimes|boolean',
            'options' => 'sometimes|array',
            'validation_rules' => 'sometimes|array',
            'field_order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
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
            'field_code.unique' => 'El código de campo ya está en uso. Por favor, elige otro.',
            'type.string' => 'El campo tipo debe ser una cadena de texto.',
            'type.max' => 'El campo tipo no debe exceder los 50 caracteres.',
            'required.boolean' => 'El campo obligatorio debe ser verdadero o falso.',
            'options.array' => 'El campo opciones debe ser un arreglo.',
            'validation_rules.string' => 'El campo reglas de validación debe ser una cadena de texto.',
            'field_order.integer' => 'El campo orden de campo debe ser un número entero.',
            'field_order.min' => 'El campo orden de campo no puede ser negativo.',
            'field_order.unique' => 'El orden de campo ya está en uso. Por favor, elige otro.',
            'is_active.boolean' => 'El campo activo debe ser verdadero o falso.',
        ];
    }
}
