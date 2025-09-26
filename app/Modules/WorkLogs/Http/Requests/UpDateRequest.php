<?php

namespace App\Modules\WorkLogs\Http\Requests;


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
class UpdateRequest extends FormRequest
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
            'date' => 'sometimes|nullable|date',
            'start_time' => 'sometimes|nullable|date_format:H:i',
            'end_time' => 'sometimes|nullable|date_format:H:i|after:start_time',
            'batch_id' => 'sometimes|nullable|integer|exists:batches,id',
            'task_description' => 'sometimes|nullable|string|max:1000',
            'notes' => 'sometimes|nullable|string|max:2000',
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
            'date.date' => 'La fecha no es válida.',
            'start_time.date_format' => 'El formato de la hora de inicio debe ser HH:MM.',
            'end_time.date_format' => 'El formato de la hora de fin debe ser HH:MM.',
            'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'total_hours.string' => 'Las horas totales deben ser una cadena de texto.',
            'total_hours.max' => 'Las horas totales no pueden exceder los 50 caracteres.',
            'overtime_hours.numeric' => 'Las horas extra deben ser un número.',
            'overtime_hours.min' => 'Las horas extra no pueden ser negativas.',
            'batch_id.string' => 'El ID del lote debe ser una cadena de texto.',
            'batch_id.max' => 'El ID del lote no puede exceder los 100 caracteres.',
            'task_description.string' => 'La descripción de la tarea debe ser una cadena de texto.',
            'task_description.max' => 'La descripción de la tarea no puede exceder los 1000 caracteres.',
            'notes.string' => 'Las notas deben ser una cadena de texto.',
            'notes.max' => 'Las notas no pueden exceder los 2000 caracteres.',
        ];
    }
}
