<?php

namespace App\Modules\Forms\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FormFieldRegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'label' => 'required|string|max:255',
            'field_code' => 'required|string|max:100|unique:form_fields,field_code,'.$this->route('form_field'),
            'type' => 'required|string|max:50',
            'required' => 'required|boolean',
            'options' => 'nullable|array',
            'validation_rules' => 'nullable|array',
            'field_order' => 'required|integer|min:0|unique:form_fields,field_order,'.$this->route('form_field'),
            'is_active' => 'required|boolean',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    public function messages()
    {
        return [
            'form_id.exists' => 'El formulario especificado no existe.',
            'label.required' => 'El campo etiqueta es obligatorio.',
            'label.string' => 'El campo etiqueta debe ser una cadena de texto.',
            'label.max' => 'El campo etiqueta no debe exceder los 255 caracteres.',
            'field_code.required' => 'El campo código de campo es obligatorio.',
            'field_code.string' => 'El campo código de campo debe ser una cadena de texto.',
            'field_code.max' => 'El campo código de campo no debe exceder los 100 caracteres.',
            'field_code.unique' => 'El código de campo ya está en uso. Por favor, elige otro.',
            'type.required' => 'El campo tipo es obligatorio.',
            'type.string' => 'El campo tipo debe ser una cadena de texto.',
            'type.max' => 'El campo tipo no debe exceder los 50 caracteres.',
            'required.required' => 'El campo obligatorio es obligatorio.',
            'required.boolean' => 'El campo obligatorio debe ser verdadero o falso.',
            'options.array' => 'El campo opciones debe ser un arreglo.',
            'validation_rules.string' => 'El campo reglas de validación debe ser una cadena de texto.',
            'field_order.required' => 'El campo orden de campo es obligatorio.',
            'field_order.integer' => 'El campo orden de campo debe ser un número entero.',
            'field_order.min' => 'El campo orden de campo no puede ser negativo.',
            'field_order.unique' => 'El orden de campo ya está en uso. Por favor, elige otro.',
            'is_active.required' => 'El campo activo es obligatorio.',
            'is_active.boolean' => 'El campo activo debe ser verdadero o falso.',
        ];
    }
}
