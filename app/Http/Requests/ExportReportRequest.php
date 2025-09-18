<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'format' => 'nullable|string|in:csv,pdf,xlsx',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'limit' => 'nullable|integer|min:1|max:5000',
            'q' => 'nullable|string|max:255',
        ];
    }
}
