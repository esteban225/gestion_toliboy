<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'form_id' => 'required|integer|exists:forms,id',
            'values' => 'required|array|min:1',
            'values.*.field_id' => 'required|integer|exists:form_fields,id',
            'values.*.value' => 'nullable',
        ];
    }
}
