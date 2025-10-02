<?php

namespace App\Modules\Batches\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * RegisterRequest gestiona la validación de datos para el registro de lotes.
 *
 * Principio SOLID aplicado:
 * - SRP (Single Responsibility Principle): Esta clase se encarga exclusivamente de la validación y autorización
 *   de la solicitud de registro, manteniendo su responsabilidad clara y única.
 *
 * No implementa otros principios SOLID directamente, pero su diseño facilita la extensión y el mantenimiento.
 */
class BatchUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'string|max:255',
            'code' => 'string|max:100',
            'product_id' => 'nullable|integer|exists:products,id',
            'start_date' => 'date',
            'expected_end_date' => 'nullable|date|after_or_equal:start_date',
            'actual_end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'bool|max:50',
            'quantity' => 'integer|min:1',
            'defect_quantity' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'created_by' => 'nullable|integer|exists:users,id',
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
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'code.unique' => 'Este código ya está registrado.',
            'start_date.date' => 'La fecha de inicio no es una fecha válida.',
            'expected_end_date.date' => 'La fecha de finalización esperada no es una fecha válida.',
            'expected_end_date.after_or_equal' => 'La fecha de finalización esperada debe ser posterior o igual a la fecha de inicio.',
            'actual_end_date.date' => 'La fecha de finalización real no es una fecha válida.',
            'actual_end_date.after_or_equal' => 'La fecha de finalización real debe ser posterior o igual a la fecha de inicio.',
            'status.string' => 'El estado debe ser una cadena de texto.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad debe ser al menos 1.',
            'defect_quantity.integer' => 'La cantidad de defectos debe ser un número entero.',
            'defect_quantity.min' => 'La cantidad de defectos no puede ser negativa.',
            'created_by.integer' => 'El ID del creador debe ser un número entero.',
            'created_by.exists' => 'El usuario creador especificado no existe.',
        ];
    }
}
