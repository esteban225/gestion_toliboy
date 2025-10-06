<?php

namespace App\Modules\Auth\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * RegisterRequest gestiona la validación de datos para el registro de usuarios.
 *
 * - Valida que el email sea único y tenga formato correcto, y que la contraseña
 *   cumpla con los requisitos mínimos de seguridad.
 * - También maneja los mensajes de error personalizados y la respuesta en caso de
 *   validación fallida.
 */
class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6',
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
            'email.exists' => 'Este correo no está registrado.',
            'email.email' => 'Debes ingresar un correo válido.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
