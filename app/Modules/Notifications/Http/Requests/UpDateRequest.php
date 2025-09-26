<?php

namespace App\Modules\Notifications\Http\Requests;

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
class UpDateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'sometimes|required|integer|exists:users,id',
            'title' => 'sometimes|required|string|max:255',
            'message' => 'sometimes|required|string',
            'type' => 'sometimes|required|string|in:info,warning,error,success',
            'related_table' => 'sometimes|nullable|string|max:255',
            'related_id' => 'sometimes|nullable|integer',
            'expires_at' => 'nullable|date_format:Y-m-d H:i:s|after:now',
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
            'user_id.required' => 'El ID del usuario es obligatorio.',
            'user_id.integer' => 'El ID del usuario debe ser un entero.',
            'user_id.exists' => 'El usuario especificado no existe.',
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 255 caracteres.',
            'message.required' => 'El mensaje es obligatorio.',
            'message.string' => 'El mensaje debe ser una cadena de texto.',
            'type.required' => 'El tipo de notificación es obligatorio.',
            'type.string' => 'El tipo de notificación debe ser una cadena de texto.',
            'type.in' => 'El tipo de notificación debe ser uno de los siguientes: info, warning, error, success.',
            'related_table.string' => 'La tabla relacionada debe ser una cadena de texto.',
            'related_table.max' => 'La tabla relacionada no debe exceder los 255 caracteres.',
            'related_id.integer' => 'El ID relacionado debe ser un entero.',
            'expires_at.date_format' => 'La fecha de expiración debe tener el formato Y-m-d H:i:s.',
            'expires_at.after' => 'La fecha de expiración debe ser una fecha futura.',
        ];
    }
}
