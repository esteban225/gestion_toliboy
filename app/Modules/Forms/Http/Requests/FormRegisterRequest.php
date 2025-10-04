<?php

namespace App\Modules\Forms\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FormRegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:forms,code,'.$this->route('form'),
            'description' => 'nullable|string',
            'version' => 'required|string|max:50',
            'created_by' => 'nullable|integer|exists:users,id',
            'is_active' => 'required|boolean',
            'display_order' => 'required|integer|min:0|unique:forms,display_order,'.$this->route('form'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    public function messages()
    {
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.string' => 'El campo nombre debe ser una cadena de texto.',
            'name.max' => 'El campo nombre no debe exceder los 255 caracteres.',
            'code.required' => 'El campo código es obligatorio.',
            'code.string' => 'El campo código debe ser una cadena de texto.',
            'code.max' => 'El campo código no debe exceder los 100 caracteres.',
            'code.unique' => 'El código ya está en uso. Por favor, elige otro.',
            'description.string' => 'El campo descripción debe ser una cadena de texto.',
            'version.required' => 'El campo versión es obligatorio.',
            'version.string' => 'El campo versión debe ser una cadena de texto.',
            'version.max' => 'El campo versión no debe exceder los 50 caracteres.',
            'created_by.integer' => 'El campo creado por debe ser un número entero.',
            'created_by.exists' => 'El usuario especificado en creado por no existe.',
            'is_active.required' => 'El campo activo es obligatorio.',
            'is_active.boolean' => 'El campo activo debe ser verdadero o falso.',
            'display_order.required' => 'El campo orden de visualización es obligatorio.',
            'display_order.integer' => 'El campo orden de visualización debe ser un número entero.',
            'display_order.min' => 'El campo orden de visualización no puede ser negativo.',
            'display_order.unique' => 'El orden de visualización ya está en uso. Por favor, elige otro.',
        ];
    }
}
