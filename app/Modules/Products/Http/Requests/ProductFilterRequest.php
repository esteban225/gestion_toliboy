<?php

namespace App\Modules\Products\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @class ProductFilterRequest
 */
class ProductFilterRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     *
     * En este caso, la autorización se maneja fuera del FormRequest (por ejemplo, con middleware),
     * por lo que siempre retorna true para permitir la validación de los datos.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Filtros de Producto
            'name' => 'sometimes|string|max:255', // Suponiendo que el nombre es una cadena de texto
            'code' => 'sometimes|string|max:100', // Suponiendo que el código es una cadena de texto
            'category' => 'sometimes|string|max:100', // Suponiendo que la categoría es una cadena de texto
            'description' => 'sometimes|string',
            'is_active' => 'sometimes|boolean', // true o false
            'created_by' => 'sometimes|integer|exists:users,id', // Debe existir en la tabla users
            'per_page' => 'sometimes|integer|min:1|max:500', // Elementos por página
            'page' => 'sometimes|integer|min:1', // Número de página
        ];
    }

    /**
     * Maneja un intento de validación fallido.
     *
     * Lanza una HttpResponseException para retornar una respuesta JSON con
     * los errores de validación y un código de estado 422.
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

    /**
     * Obtiene los mensajes de error personalizados para las reglas de validación definidas.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Mensajes para Filtros
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'code.string' => 'El código debe ser una cadena de texto.',
            'category.string' => 'La categoría debe ser una cadena de texto.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'is_active.boolean' => 'El estado de actividad debe ser verdadero o falso.',
            'created_by.integer' => 'El ID del creador debe ser un entero.',
            'created_by.exists' => 'El usuario creador especificado no existe.',

            // Mensajes para Paginación
            'per_page.integer' => 'El número de elementos por página debe ser un entero.',
            'per_page.min' => 'El número de elementos por página debe ser al menos 1.',
            'per_page.max' => 'El número de elementos por página no puede exceder 100.',
            'page.integer' => 'El número de página debe ser un entero.',
            'page.min' => 'El número de página debe ser al menos 1.',
        ];
    }
}
