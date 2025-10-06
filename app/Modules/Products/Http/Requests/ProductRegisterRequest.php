<?php

namespace App\Modules\Products\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * ProductRegisterRequest gestiona la validación de datos para el registro de productos.
 */
class ProductRegisterRequest extends FormRequest
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
            'code' => 'required|string|max:100|unique:products,code',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'specifications' => 'nullable|array',
            'unit_price' => 'required|numeric|min:0',
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
            'name.max' => 'El nombre no debe exceder los 255 caracteres.',

            'code.required' => 'El código es obligatorio.',
            'code.string' => 'El código debe ser una cadena de texto.',
            'code.max' => 'El código no debe exceder los 100 caracteres.',
            'code.unique' => 'Este código ya está registrado.',

            'category.string' => 'La categoría debe ser una cadena de texto.',
            'category.max' => 'La categoría no debe exceder los 100 caracteres.',

            'description.string' => 'La descripción debe ser una cadena de texto.',

            'unit_price.required' => 'El precio unitario es obligatorio.',
            'unit_price.numeric' => 'El precio unitario debe ser un valor numérico.',
            'unit_price.min' => 'El precio unitario no puede ser negativo.',

            'specifications.string' => 'Las especificaciones deben ser una cadena de texto.',

            'is_active.required' => 'El estado es obligatorio.',
            'is_active.boolean' => 'El estado debe ser verdadero o falso.',

            'created_by.integer' => 'El ID del creador debe ser un número entero.',
            'created_by.exists' => 'El usuario creador especificado no existe en el sistema.',
        ];
    }
}
