<?php

namespace App\Modules\Forms\Domain\Entities;

use App\Models\FormField; // Ajusta el namespace de tu modelo Eloquent real

class FormFieldEntity
{
    private ?int $id;

    private int $form_id;

    private string $label;

    private string $field_code;

    private string $type;

    private bool $required;

    private ?array $options;

    private ?array $validation_rules;

    private int $field_order;

    private bool $is_active;

    public function __construct(
        ?int $id,
        int $form_id,
        string $label,
        string $field_code,
        string $type,
        bool $required,
        ?array $options,
        ?array $validation_rules,
        int $field_order,
        bool $is_active
    ) {
        $this->id = $id;
        $this->form_id = $form_id;
        $this->label = $label;
        $this->field_code = $field_code;
        $this->type = $type;
        $this->required = $required;
        $this->options = $options;
        $this->validation_rules = $validation_rules;
        $this->field_order = $field_order;
        $this->is_active = $is_active;
    }

    /* ============================
     * Getters
     * ============================ */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormId(): int
    {
        return $this->form_id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getFieldCode(): string
    {
        return $this->field_code;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function getValidationRules(): ?array
    {
        return $this->validation_rules;
    }

    public function getFieldOrder(): int
    {
        return $this->field_order;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    /* ============================
     * Setters
     * ============================ */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setFormId(int $form_id): void
    {
        $this->form_id = $form_id;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function setFieldCode(string $field_code): void
    {
        $this->field_code = $field_code;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    public function setOptions(?array $options): void
    {
        $this->options = $options;
    }

    public function setValidationRules(?array $validation_rules): void
    {
        $this->validation_rules = $validation_rules;
    }

    public function setFieldOrder(int $field_order): void
    {
        $this->field_order = $field_order;
    }

    public function setIsActive(bool $is_active): void
    {
        $this->is_active = $is_active;
    }

    /* ============================
     * Conversión desde array
     * ============================ */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['form_id'],
            $data['label'],
            $data['field_code'],
            $data['type'],
            $data['required'],
            $data['options'] ?? null,
            $data['validation_rules'] ?? null,
            $data['field_order'],
            $data['is_active']
        );
    }

    /* ============================
     * Conversión a array
     * ============================ */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'form_id' => $this->form_id,
            'label' => $this->label,
            'field_code' => $this->field_code,
            'type' => $this->type,
            'required' => $this->required,
            'options' => $this->options,
            'validation_rules' => $this->validation_rules,
            'field_order' => $this->field_order,
            'is_active' => $this->is_active,
        ];
    }

    /* ============================
     * Conversión desde modelo Eloquent
     * ============================ */
    public static function fromModel(FormField $model): self
    {
        return new self(
            $model->id,
            $model->form_id,
            $model->label,
            $model->field_code,
            $model->type,
            (bool) $model->required,
            $model->options ? (array) $model->options : null,
            $model->validation_rules ? (array) $model->validation_rules : null,
            $model->field_order,
            (bool) $model->is_active
        );
    }
}
