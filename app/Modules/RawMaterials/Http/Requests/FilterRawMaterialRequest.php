<?php

namespace App\Modules\RawMaterials\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * FilterRawMaterialRequest gestiona la validación de datos para el filtrado de materias primas.
 */
class FilterRawMaterialRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:100',
            'is_active' => 'sometimes|boolean',
            'created_by' => 'sometimes|integer|exists:users,id',
            'per_page' => 'sometimes|integer|min:1|max:100', // Elementos por página
            'page' => 'sometimes|integer|min:1', // Número de página
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
            'code.string' => 'El código debe ser una cadena de texto.',
            'is_active.boolean' => 'El campo activo debe ser true o false.',
            'created_by.integer' => 'El ID del creador debe ser un número entero.',
            'created_by.exists' => 'El usuario creador especificado no existe.',
            'per_page.integer' => 'El número de elementos por página debe ser un entero.',
            'per_page.min' => 'El número de elementos por página debe ser al menos 1.',
            'per_page.max' => 'El número de elementos por página no puede exceder 100.',
            'page.integer' => 'El número de página debe ser un entero.',
            'page.min' => 'El número de página debe ser al menos 1.',
        ];
    }
}
