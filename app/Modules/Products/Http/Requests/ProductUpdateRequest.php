<?php

namespace App\Modules\Products\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * ProductUpdateRequest gestiona la validación de datos para la actualización de productos.
 */
class ProductUpdateRequest extends FormRequest
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
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'specifications' => 'nullable|array',
            'unit_price' => 'numeric|min:0',
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
            'name.max' => 'El nombre no debe exceder los 255 caracteres.',

            'code.string' => 'El código debe ser una cadena de texto.',
            'code.max' => 'El código no debe exceder los 100 caracteres.',

            'category.string' => 'La categoría debe ser una cadena de texto.',
            'category.max' => 'La categoría no debe exceder los 100 caracteres.',

            'description.string' => 'La descripción debe ser una cadena de texto.',

            'specifications.array' => 'Las especificaciones deben ser un arreglo.',

            'unit_price.numeric' => 'El precio unitario debe ser un número.',
            'unit_price.min' => 'El precio unitario no puede ser negativo.',

            'is_active.boolean' => 'El campo activo debe ser verdadero o falso.',

            'created_by.integer' => 'El ID del creador debe ser un número entero.',
            'created_by.exists' => 'El ID del creador no existe en la base de datos.',
        ];
    }
}
