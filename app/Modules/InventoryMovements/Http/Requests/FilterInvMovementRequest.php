<?php

namespace App\Modules\InventoryMovements\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * FilterInvMovementRequest gestiona la validación de datos para el filtrado de movimientos de inventario.
 */
class FilterInvMovementRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'movement_type' => 'sometimes|string|in:in,out,adjustment', // Tipos de movimiento permitidos
            'raw_material_id' => 'sometimes|integer|exists:raw_materials,id', // ID de materia prima existente
            'batch_id' => 'sometimes|integer|exists:batches,id', // ID de lote existente
            'created_by' => 'sometimes|integer|exists:users,id', // ID de usuario existente
            'production_line' => 'sometimes|string', // Línea de producción
            'created_at' => 'sometimes|date', // Fecha de creación
            'page' => 'sometimes|integer|min:1', // Página para paginación
            'per_page' => 'sometimes|integer|min:1|max:500', // Ítems por página para paginación
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422));
    }

    public function messages(): array
    {
        return [
            'movement_type.string' => 'El tipo de movimiento debe ser una cadena de texto.',
            'movement_type.in' => 'El tipo de movimiento debe ser uno de los siguientes: in, out, adjustment.',
            'raw_material_id.integer' => 'El ID de la materia prima debe ser un número entero.',
            'raw_material_id.exists' => 'La materia prima especificada no existe.',
            'batch_id.integer' => 'El ID del lote debe ser un número entero.',
            'batch_id.exists' => 'El lote especificado no existe.',
            'created_by.integer' => 'El ID del creador debe ser un número entero.',
            'created_by.exists' => 'El usuario creador especificado no existe.',
        ];
    }
}
