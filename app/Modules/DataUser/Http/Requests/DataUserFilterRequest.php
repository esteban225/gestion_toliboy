<?php

namespace App\Modules\DataUser\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Filtros para listar y buscar datos de usuario.
 */
class DataUserFilterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'sometimes|exists:users,id|unique:personal_data,user_id', // Asegura que el user_id existe en la tabla users y es único en personal_data
            'num_phone' => 'sometimes|string|max:20', // Teléfono principal, si está en el parametro
            'num_phone_alt' => 'sometimes|string|max:20', // Teléfono alternativo, si está en el parametro
            'num_identification' => 'sometimes|string|max:50|unique:personal_data,num_identification', // Identificación única y si está en el parametro
            'identification_type' => 'sometimes|string|max:45', // Tipo de identificación, si está en el parametro
            'address' => 'sometimes|string|max:45', // Dirección, si está en el parametro
            'emergency_contact' => 'sometimes|string|max:100', // Contacto de emergencia, si está en el parametro
            'emergency_phone' => 'sometimes|string|max:25', // Teléfono de emergencia, si está en el parametro
            'per_page' => 'sometimes|integer|min:1|max:500', // Número de resultados por página para paginación
            'page' => 'sometimes|integer|min:1', // Número de página para paginación
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
            'user_id.exists' => 'El ID de usuario no existe.',
            'user_id.unique' => 'El ID de usuario ya tiene datos asociados.',
            'num_phone.string' => 'El número de teléfono debe ser una cadena de texto.',
            'num_phone.max' => 'El número de teléfono no debe exceder los 20 caracteres.',
            'num_phone_alt.string' => 'El número de teléfono alternativo debe ser una cadena de texto.',
            'num_phone_alt.max' => 'El número de teléfono alternativo no debe exceder los 20 caracteres.',
            'num_identification.string' => 'El número de identificación debe ser una cadena de texto.',
            'num_identification.max' => 'El número de identificación no debe exceder los 50 caracteres.',
            'num_identification.unique' => 'El número de identificación ya está registrado.',
            'identification_type.string' => 'El tipo de identificación debe ser una cadena de texto.',
            'identification_type.max' => 'El tipo de identificación no debe exceder los 45 caracteres.',
            'address.string' => 'La dirección debe ser una cadena de texto.',
            'address.max' => 'La dirección no debe exceder los 45 caracteres.',
            'emergency_contact.string' => 'El contacto de emergencia debe ser una cadena de texto.',
            'emergency_contact.max' => 'El contacto de emergencia no debe exceder los 100 caracteres.',
            'emergency_phone.string' => 'El teléfono de emergencia debe ser una cadena de texto.',
            'emergency_phone.max' => 'El teléfono de emergencia no debe exceder los 25 caracteres.',
        ];
    }
}
