<?php

namespace App\Modules\WorkLogs\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * WorkLogUpdateRequest gestiona la validación de datos para el filtro de horas de trabajo.
 */
class WorkLogFilterRequest extends FormRequest
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
            'per_page' => 'sometimes|nullable|integer|min:1|max:500',
            'page' => 'sometimes|nullable|integer|min:1',
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
