<?php

namespace App\Modules\Forms\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FormResponseStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'form_id' => 'required|exists:forms,id',
            'batch_id' => 'nullable|exists:batches,id',
            'values' => 'required|array',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    public function messages()
    {
        return [
            'form_id.required' => 'El ID del formulario es obligatorio.',
            'form_id.exists' => 'El formulario especificado no existe.',
            'batch_id.exists' => 'El lote especificado no existe.',
            'values.required' => 'Los valores del formulario son obligatorios.',
            'values.array' => 'Los valores del formulario deben ser un arreglo.',
        ];
    }
}
