<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'batch_id' => 'nullable|integer|exists:batches,id',
            'task_description' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'end_time.after' => 'El tiempo de fin debe ser posterior al inicio.',
        ];
    }
}
