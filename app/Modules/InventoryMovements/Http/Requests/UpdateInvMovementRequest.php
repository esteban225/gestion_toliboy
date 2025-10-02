<?php

namespace App\Modules\InventoryMovements\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * RegisterRequest gestiona la validación de datos para el registro de usuarios.
 *
 * Principio SOLID aplicado:
 * - SRP (Single Responsibility Principle): Esta clase se encarga exclusivamente de la validación y autorización
 *   de la solicitud de registro, manteniendo su responsabilidad clara y única.
 *
 * No implementa otros principios SOLID directamente, pero su diseño facilita la extensión y el mantenimiento.
 */
class UpdateInvMovementRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'raw_material_id' => 'sometimes|required|integer|exists:raw_materials,id',
            'batch_id' => 'sometimes|nullable|integer|exists:batches,id',
            'movement_type' => 'sometimes|required|string|in:in,out,adjustment',
            'quantity' => 'sometimes|required|numeric|min:0.01',
            'unit_cost' => 'sometimes|required|numeric|min:0',
            'notes' => 'sometimes|nullable|string',
            'created_by' => 'sometimes|nullable|integer|exists:users,id',
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
            'raw_material_id.required' => 'El ID de la materia prima es obligatorio.',
            'raw_material_id.integer' => 'El ID de la materia prima debe ser un entero.',
            'raw_material_id.exists' => 'La materia prima especificada no existe.',
            'batch_id.integer' => 'El ID del lote debe ser un entero.',
            'batch_id.exists' => 'El lote especificado no existe.',
            'movement_type.required' => 'El tipo de movimiento es obligatorio.',
            'movement_type.string' => 'El tipo de movimiento debe ser una cadena de texto.',
            'movement_type.in' => 'El tipo de movimiento debe ser uno de los siguientes: IN, OUT, ADJUSTMENT.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un valor numérico.',
            'quantity.min' => 'La cantidad debe ser al menos 0.01.',
            'unit_cost.required' => 'El costo unitario es obligatorio.',
            'unit_cost.numeric' => 'El costo unitario debe ser un valor numérico.',
            'unit_cost.min' => 'El costo unitario no puede ser negativo.',
            'notes.string' => 'Las notas deben ser una cadena de texto.',
            'created_by.integer' => 'El ID del creador debe ser un entero.',
            'created_by.exists' => 'El usuario creador especificado no existe.',
        ];
    }
}
