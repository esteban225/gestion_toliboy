<?php

namespace App\Modules\Forms\Domain\Services;

use App\Models\Form;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FormFieldValidatorService
{
    /**
     * Valida los valores de los campos de un formulario dinámicamente
     *
     * Retorna:
     *  - null si la validación pasa
     *  - array de errores si falla
     */
    public function validate(Form $form, array $values): ?array
    {
        $validationRules = [];
        $messages = [];

        foreach ($form->form_fields as $field) {
            if (! $field->is_active) {
                continue;
            }

            $rules = [];

            // Required o nullable
            if ($field->required) {
                $rules[] = $field->type === 'file' ? 'required_without:id' : 'required';
            } else {
                $rules[] = 'nullable';
            }

            // Reglas por tipo
            switch ($field->type) {
                case 'text':
                case 'textarea':
                    $rules[] = 'string';
                    break;

                case 'number':
                    $rules[] = 'numeric';
                    break;

                case 'email':
                    $rules[] = 'email';
                    break;

                case 'date':
                    $rules[] = 'date';
                    break;

                case 'time':
                    $rules[] = 'date_format:H:i';
                    break;

                case 'datetime':
                    $rules[] = 'date_format:Y-m-d H:i:s';
                    break;

                case 'select':
                case 'radio':
                    if (! empty($field->options)) {
                        $options = $this->decodeOptions($field->options);
                        if ($options) {
                            $rules[] = Rule::in($options);
                        }
                    }
                    break;

                case 'checkbox':
                case 'multiselect':
                    $rules[] = 'array';
                    if (! empty($field->options)) {
                        $options = $this->decodeOptions($field->options);
                        if ($options) {
                            $validationRules["values.{$field->field_code}.*"] = [Rule::in($options)];
                        }
                    }
                    break;

                case 'file':
                    $rules[] = 'file';
                    $rules[] = 'mimes:jpeg,png,jpg,pdf,doc,docx,xls,xlsx';
                    $rules[] = 'max:10240';
                    break;

                default:
                    // Cualquier tipo desconocido se valida como string
                    $rules[] = 'string';
                    break;
            }

            // Reglas personalizadas (si existen)
            if (! empty($field->validation_rules)) {
                $customRules = is_array($field->validation_rules)
                    ? $field->validation_rules
                    : json_decode($field->validation_rules, true);
                if (is_array($customRules)) {
                    foreach ($customRules as $customRule) {
                        $normalized = $this->normalizeCustomRule($customRule);
                        if ($normalized) {
                            $rules[] = $normalized;
                        }
                    }
                }
            }

            // Asignar reglas al campo
            $key = "values.{$field->field_code}";
            if (isset($validationRules[$key])) {
                $validationRules[$key] = array_unique(array_merge($validationRules[$key], $rules));
            } else {
                $validationRules[$key] = $rules;
            }

            // Mensaje de required personalizado
            $messages["{$key}.required"] = "El campo '{$field->label}' es obligatorio.";
        }

        // Validar
        $validator = Validator::make(['values' => $values], $validationRules, $messages);

        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }

        return null;
    }

    /**
     * Decodifica opciones JSON o array
     */
    private function decodeOptions($options): ?array
    {
        if (is_array($options)) {
            return array_map(fn($item) => is_array($item) && isset($item['value']) ? $item['value'] : $item, $options);
        }

        $decoded = json_decode($options, true);
        if (is_array($decoded)) {
            return array_map(fn($item) => is_array($item) && isset($item['value']) ? $item['value'] : $item, $decoded);
        }

        return null;
    }

    /**
     * Normaliza reglas personalizadas
     */
    private function normalizeCustomRule(mixed $rule): ?string
    {
        if (! is_string($rule)) {
            return null;
        }

        $r = trim($rule);
        if ($r === '') {
            return null;
        }

        // Si es una regla inválida (como AllowedTypes), la ignoramos
        $invalids = ['AllowedTypes', 'AllowedType', 'allowedTypes'];
        if (in_array($r, $invalids, true)) {
            return null;
        }

        // Si ya tiene formato rule:option (min:3, regex:/.../)
        if (strpos($r, ':') !== false) {
            return $r;
        }

        // Si parece una regex
        if (preg_match('/^\/.*\/[a-zA-Z]*$/', $r)) {
            return 'regex:' . $r;
        }

        if (preg_match('/^[\^\\[]|\\\\[wdDsS]|[\(\)\[\]\.\+\*\?\|]/', $r)) {
            $escaped = str_replace('/', '\\/', $r);
            return 'regex:/' . $escaped . '/u';
        }

        return $r;
    }
}
