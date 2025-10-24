<?php

namespace App\Modules\Users\Http\Requests;

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
class UserUpDateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    /**
     * @bodyParam name string required Nombre del usuario.
     * @bodyParam email string required Email único.
     * @bodyParam password string nullable Nueva contraseña.
     * @bodyParam role_id string nullable Rol asignado.
     * @bodyParam position string nullable Cargo.
     * @bodyParam is_active boolean nullable Estado activo (1/0).
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255',
            'password' => 'sometimes|string|min:8',
            'role_id' => 'integer|exists:roles,id',
            'position' => 'string|max:100',
            'is_active' => 'boolean',
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
            'email.unique' => 'Este correo ya está registrado.',
            'email.email' => 'Debes ingresar un correo válido.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'role_id.exists' => 'El rol especificado no existe.',
            'position.max' => 'La posición no puede tener más de 100 caracteres.',
            'is_active.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}
