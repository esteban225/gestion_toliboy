<?php

namespace App\Modules\Users\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Parametros que gestiona la validación de datos para el filtrado de usuarios.
 */
class UserFilterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255', // Modificado a 'sometimes' para filtros opcionales
            'is_active' => 'sometimes|boolean', // Modificado a 'sometimes' para filtros opcionales
            'per_page' => 'sometimes|integer|min:1|max:100', // Paginación, por defecto 15 por página
            'page' => 'sometimes|integer|min:1', // Página actual, por defecto 1
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
        ];
    }
}
