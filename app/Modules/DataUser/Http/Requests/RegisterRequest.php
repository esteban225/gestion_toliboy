<?php

namespace App\Modules\DataUser\Http\Requests;

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
            'user_id' => 'required|exists:users,id|unique:personal_data,user_id',
            'num_phone' => 'nullable|string|max:20',
            'num_phone_alt' => 'nullable|string|max:20',
            // 'personal_data' is likely a typo, it should be 'personal_data'
            'num_identification' => 'required|string|max:50|unique:personal_data,num_identification',
            'identification_type' => 'required|string|max:45',
            'address' => 'nullable|string|max:45',
            'emergency_contact' => 'required|string|max:100',
            'emergency_phone' => 'required|string|max:25',
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
            'user_id.required' => 'El ID de usuario es obligatorio.',
            'user_id.exists' => 'El ID de usuario no existe.',
            'user_id.unique' => 'El ID de usuario ya tiene datos asociados.',
            'num_phone.string' => 'El número de teléfono debe ser una cadena de texto.',
            'num_phone.max' => 'El número de teléfono no debe exceder los 20 caracteres.',
            'num_phone_alt.string' => 'El número de teléfono alternativo debe ser una cadena de texto.',
            'num_phone_alt.max' => 'El número de teléfono alternativo no debe exceder los 20 caracteres.',
            'num_identification.required' => 'El número de identificación es obligatorio.',
            'num_identification.string' => 'El número de identificación debe ser una cadena de texto.',
            'num_identification.max' => 'El número de identificación no debe exceder los 50 caracteres.',
            'num_identification.unique' => 'El número de identificación ya está registrado.',
            'identification_type.required' => 'El tipo de identificación es obligatorio.',
            'identification_type.string' => 'El tipo de identificación debe ser una cadena de texto.',
            'identification_type.max' => 'El tipo de identificación no debe exceder los 45 caracteres.',
            'address.string' => 'La dirección debe ser una cadena de texto.',
            'address.max' => 'La dirección no debe exceder los 45 caracteres.',
            'emergency_contact.required' => 'El contacto de emergencia es obligatorio.',
            'emergency_contact.string' => 'El contacto de emergencia debe ser una cadena de texto.',
            'emergency_contact.max' => 'El contacto de emergencia no debe exceder los 100 caracteres.',
            'emergency_phone.required' => 'El teléfono de emergencia es obligatorio.',
            'emergency_phone.string' => 'El teléfono de emergencia debe ser una cadena de texto.',
            'emergency_phone.max' => 'El teléfono de emergencia no debe exceder los 25 caracteres.',

        ];
    }
}
