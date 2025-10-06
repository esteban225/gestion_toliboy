<?php

namespace App\Modules\Forms\Domain\Services;

use App\Models\Form;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FormFieldValidatorService
{
    /**
     * Valida los valores de los campos de un formulario dinámicamente
     *
     * @param Form $form
     * @param array $values
     * @throws ValidationException
     */
    public function validate(Form $form, array $values): void
    {
        $validationRules = [];
        $messages = [];
        foreach ($form->form_fields as $field) {
            if (! $field->is_active) continue;
            $rules = [];
            if ($field->required) {
                $rules[] = $field->type === 'file' ? 'required_without:id' : 'required';
            } else {
                $rules[] = 'nullable';
            }
            switch ($field->type) {
                case 'text':
                case 'textarea': $rules[] = 'string'; break;
                case 'number': $rules[] = 'numeric'; break;
                case 'date': $rules[] = 'date'; break;
                case 'time': $rules[] = 'date_format:H:i'; break;
                case 'datetime': $rules[] = 'date_format:Y-m-d H:i:s'; break;
                case 'select':
                case 'radio':
                    if (!empty($field->options)) {
                        $options = is_array($field->options) ? $field->options : json_decode($field->options, true);
                        if (is_array($options)) {
                            $allowed = array_map(fn($item) => is_array($item) && isset($item['value']) ? $item['value'] : $item, $options);
                            $rules[] = 'in:' . implode(',', $allowed);
                        }
                    }
                    break;
                case 'checkbox':
                case 'multiselect':
                    $rules[] = 'array';
                    if (!empty($field->options)) {
                        $options = is_array($field->options) ? $field->options : json_decode($field->options, true);
                        if (is_array($options)) {
                            $allowed = array_map(fn($item) => is_array($item) && isset($item['value']) ? $item['value'] : $item, $options);
                            $fieldName = 'values.' . $field->name;
                            $validationRules[$fieldName] = ['array'];
                            $validationRules[$fieldName . '.*'] = ['in:' . implode(',', $allowed)];
                        }
                    }
                    break;
                case 'file':
                    $rules[] = 'file';
                    $rules[] = 'mimes:jpeg,png,jpg,pdf,doc,docx,xls,xlsx';
                    $rules[] = 'max:10240';
                    break;
            }
            if (!empty($field->validation_rules)) {
                $customRules = is_array($field->validation_rules) ? $field->validation_rules : json_decode($field->validation_rules, true);
                if (is_array($customRules)) {
                    $rules = array_merge($rules, $customRules);
                }
            }
            if (!empty($rules)) {
                $validationRules["values.{$field->field_code}"] = implode('|', $rules);
                $messages["values.{$field->field_code}.required"] = "El campo '{$field->label}' es obligatorio.";
            }
        }
        $validator = Validator::make(['values' => $values], $validationRules, $messages);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
