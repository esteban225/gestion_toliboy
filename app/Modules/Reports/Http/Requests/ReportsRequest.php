<?php

namespace App\Modules\Reports\Http\Requests;

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
class ReportsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'rows' => 'required|array',
            'headings' => 'required|array',
            'title' => 'required|string|max:255',
            'format' => 'nullable|string|in:pdf,xlsx,csv',
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
            'rows.required' => 'Los datos del reporte son obligatorios.',
            'rows.array' => 'Los datos del reporte deben ser un arreglo.',
            'headings.required' => 'Los encabezados del reporte son obligatorios.',
            'headings.array' => 'Los encabezados del reporte deben ser un arreglo.',
            'title.required' => 'El título del reporte es obligatorio.',
            'title.string' => 'El título del reporte debe ser una cadena de texto.',
            'title.max' => 'El título del reporte no debe exceder los 255 caracteres.',
            'format.string' => 'El formato debe ser una cadena de texto.',
            'format.in' => 'El formato debe ser uno de los siguientes: pdf, xlsx, csv.',
        ];
    }
}
