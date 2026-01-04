<?php

namespace App\Modules\Batches\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * FilterBatchRequest gestiona la validación de datos para el filtrado de lotes.
 */
class FilterBatchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255', // Nombre del lote
            'code' => 'sometimes|string|max:100', // Código del lote
            'product_id' => 'sometimes|integer|exists:products,id', // ID del producto asociado
            'status' => 'sometimes', // Estado del lote
            'created_at' => 'sometimes|date', // Fecha de creación
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }

    public function messages(): array
    {
        return [
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'code.string' => 'El código debe ser una cadena de texto.',
            'product_id.integer' => 'El ID del producto debe ser un número entero.',
            'product_id.exists' => 'El producto especificado no existe.',
            'status.in' => 'El estado debe ser "active" o "inactive".',
            'created_at.date' => 'La fecha de creación no es una fecha válida.',
        ];
    }
}
