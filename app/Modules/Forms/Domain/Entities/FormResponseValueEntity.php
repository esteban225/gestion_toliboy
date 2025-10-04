<?php

namespace App\Modules\Forms\Domain\Entities;

use App\Models\FormResponseValue; // Ajusta el namespace de tu modelo Eloquent real

class FormResponseValueEntity
{
    private ?int $id;

    private int $response_id;

    private int $field_id;

    private ?string $value;

    private ?string $file_path;

    public function __construct(
        ?int $id,
        int $response_id,
        int $field_id,
        ?string $value,
        ?string $file_path
    ) {
        $this->id = $id;
        $this->response_id = $response_id;
        $this->field_id = $field_id;
        $this->value = $value;
        $this->file_path = $file_path;
    }

    /* ============================
     * Getters
     * ============================ */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResponseId(): int
    {
        return $this->response_id;
    }

    public function getFieldId(): int
    {
        return $this->field_id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getFilePath(): ?string
    {
        return $this->file_path;
    }

    /* ============================
     * Setters
     * ============================ */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setResponseId(int $response_id): void
    {
        $this->response_id = $response_id;
    }

    public function setFieldId(int $field_id): void
    {
        $this->field_id = $field_id;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function setFilePath(?string $file_path): void
    {
        $this->file_path = $file_path;
    }

    /* ============================
     * Conversión desde array
     * ============================ */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['response_id'],
            $data['field_id'],
            $data['value'] ?? null,
            $data['file_path'] ?? null
        );
    }

    /* ============================
     * Conversión a array
     * ============================ */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'response_id' => $this->response_id,
            'field_id' => $this->field_id,
            'value' => $this->value,
            'file_path' => $this->file_path,
        ];
    }

    /* ============================
     * Conversión desde modelo Eloquent
     * ============================ */
    public static function fromModel(FormResponseValue $model): self
    {
        return new self(
            $model->id,
            $model->response_id,
            $model->field_id,
            $model->value,
            $model->file_path
        );
    }
}
