<?php

namespace App\Modules\RawMaterials\Http\Requests;

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
class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100',
            'description' => 'nullable|string',
            'unit_of_measure' => 'required|string|max:50',
            'stock' => 'required|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean',
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
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'code.unique' => 'Este código ya está registrado.',
            'unit_of_measure.required' => 'La unidad de medida es obligatoria.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.numeric' => 'El stock debe ser un valor numérico.',
            'stock.min' => 'El stock no puede ser negativo.',
            'min_stock.numeric' => 'El stock mínimo debe ser un valor numérico.',
            'min_stock.min' => 'El stock mínimo no puede ser negativo.',
            'is_active.boolean' => 'El estado debe ser verdadero o falso.',
            'created_by.exists' => 'El usuario creador especificado no existe.',
        ];
    }
}
