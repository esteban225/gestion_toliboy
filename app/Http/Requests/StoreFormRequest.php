<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null; // roles checked by routes/middleware
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:191',
            'description' => 'nullable|string|max:2000',
            'fields' => 'nullable|array',
            'fields.*.label' => 'required_with:fields|string|max:191',
            'fields.*.type' => 'required_with:fields|string|in:text,textarea,select,checkbox,number,date,file',
            'fields.*.options' => 'nullable|array',
        ];
    }
}
