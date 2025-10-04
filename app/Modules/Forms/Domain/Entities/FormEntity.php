<?php

namespace App\Modules\Forms\Domain\Entities;

use App\Models\Form; // Ajusta el namespace de tu modelo Eloquent real

class FormEntity
{
    private ?int $id;

    private string $name;

    private string $code;

    private ?string $description;

    private string $version;

    private ?int $created_by;

    private bool $is_active;

    private int $display_order;

    public function __construct(
        ?int $id,
        string $name,
        string $code,
        ?string $description,
        string $version,
        ?int $created_by,
        bool $is_active,
        int $display_order
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->description = $description;
        $this->version = $version;
        $this->created_by = $created_by;
        $this->is_active = $is_active;
        $this->display_order = $display_order;
    }

    /* ============================
     * Getters
     * ============================ */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getCreatedBy(): ?int
    {
        return $this->created_by;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getDisplayOrder(): int
    {
        return $this->display_order;
    }

    /* ============================
     * Setters
     * ============================ */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function setCreatedBy(?int $created_by): void
    {
        $this->created_by = $created_by;
    }

    public function setIsActive(bool $is_active): void
    {
        $this->is_active = $is_active;
    }

    public function setDisplayOrder(int $display_order): void
    {
        $this->display_order = $display_order;
    }

    /* ============================
     * Conversión desde array
     * ============================ */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'],
            $data['code'],
            $data['description'] ?? null,
            $data['version'],
            $data['created_by'] ?? null,
            $data['is_active'],
            $data['display_order']
        );
    }

    /* ============================
     * Conversión a array
     * ============================ */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'version' => $this->version,
            'created_by' => $this->created_by,
            'is_active' => $this->is_active,
            'display_order' => $this->display_order,
        ];
    }

    /* ============================
     * Conversión desde modelo Eloquent
     * ============================ */
    public static function fromModel(Form $model): self
    {
        return new self(
            $model->id,
            $model->name,
            $model->code,
            $model->description,
            $model->version,
            $model->created_by,
            (bool) $model->is_active,
            $model->display_order
        );
    }
}
