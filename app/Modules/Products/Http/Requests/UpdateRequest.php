<?php

namespace App\Modules\Products\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * UpdateRequest gestiona la validación de datos para la actualización de productos.
 *
 * Principio SOLID aplicado:
 * - SRP (Single Responsibility Principle): Esta clase se encarga exclusivamente de la validación y autorización
 *   de la solicitud de actualización, manteniendo su responsabilidad clara y única.
 *
 * No implementa otros principios SOLID directamente, pero su diseño facilita la extensión y el mantenimiento.
 */
class UpdateRequest extends FormRequest
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
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'stock' => 'numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
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
            'price.numeric' => 'El precio debe ser un valor numérico.',
            'stock.numeric' => 'El stock debe ser un valor numérico.',
            'is_active.boolean' => 'El estado debe ser verdadero o falso.',
            'created_by.integer' => 'El ID del creador debe ser un número entero.',
            'created_by.exists' => 'El usuario creador no existe.',
        ];
    }
}
