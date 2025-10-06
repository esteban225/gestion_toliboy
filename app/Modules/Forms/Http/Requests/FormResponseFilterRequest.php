<?php

namespace App\Modules\Forms\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FormResponseFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'form_id' => 'sometimes|integer|exists:forms,id',
            'user_id' => 'sometimes|integer|exists:users,id',
            'batch_id' => 'sometimes|integer|exists:batches,id',
            'status' => 'sometimes|string|in:pending,in_progress,completed,approved,rejected',
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
            'form_id.integer' => 'El ID del formulario debe ser un número entero.',
            'form_id.exists' => 'El formulario especificado no existe.',
            'user_id.integer' => 'El ID del usuario debe ser un número entero.',
            'user_id.exists' => 'El usuario especificado no existe.',
            'batch_id.integer' => 'El ID del lote debe ser un número entero.',
            'batch_id.exists' => 'El lote especificado no existe.',
            'status.string' => 'El estado debe ser una cadena de texto.',
            'status.in' => 'El estado debe ser uno de los siguientes: pending, in_progress, completed, approved, rejected.',
            'page.integer' => 'La página debe ser un número entero.',
            'page.min' => 'La página debe ser al menos 1.',
            'per_page.integer' => 'El número de elementos por página debe ser un número entero.',
            'per_page.min' => 'El número de elementos por página debe ser al menos 1.',
            'per_page.max' => 'El número de elementos por página no puede exceder de 100.',

        ];
    }
}
