<?php

namespace App\Modules\Auth\Http\Requests;

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
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|numeric|exists:roles,id',
            'position' => 'required|string|max:255',

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
            'email.unique' => 'Este correo ya está registrado.',
            'email.email' => 'Debes ingresar un correo válido.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
            'name.required' => 'El nombre es obligatorio.',
            'position.required' => 'El cargo es obligatorio.',
            'position.max' => 'El cargo no debe exceder los 255 caracteres.',
            'role_id.required' => 'El rol es obligatorio.',
            'name.max' => 'El nombre no debe exceder los 255 caracteres.',
            'password.required' => 'La contraseña es obligatoria.',
            'email.required' => 'El correo es obligatorio.',

        ];
    }
}
