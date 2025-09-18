<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:191',
            'description' => 'sometimes|nullable|string|max:2000',
            'fields' => 'sometimes|array',
            'fields.*.label' => 'required_with:fields|string|max:191',
            'fields.*.type' => 'required_with:fields|string|in:text,textarea,select,checkbox,number,date,file',
        ];
    }
}
