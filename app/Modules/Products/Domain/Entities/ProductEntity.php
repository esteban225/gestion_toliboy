<?php

namespace App\Modules\Products\Domain\Entities;

class ProductEntity
{
    private ?string $id;

    private string $name;

    private string $code;

    private ?string $category;

    private ?string $description;

    private ?array $specifications;

    private float $unit_price;

    private bool $is_active;

    private string $created_by;

    public function __construct(
        ?string $id,
        string $name,
        string $code,
        ?string $category,
        ?string $description,
        ?array $specifications,
        float $unit_price,
        bool $is_active,
        string $created_by
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->category = $category;
        $this->description = $description;
        $this->specifications = $specifications;
        $this->unit_price = $unit_price;
        $this->is_active = $is_active;
        $this->created_by = $created_by;
    }

    // ========= GETTERS =========
    public function getId(): ?string
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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getUnitPrice(): float
    {
        return $this->unit_price;
    }

    public function getSpecifications(): ?array
    {
        return $this->specifications;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getCreatedBy(): string
    {
        return $this->created_by;
    }

    // ========= SETTERS =========
    public function setId(?string $id): void
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

    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    public function setDescription(?array $description): void
    {
        $this->description = $description;
    }

    public function setUnitPrice(float $unit_price): void
    {
        $this->unit_price = $unit_price;
    }

    public function setSpecifications(?string $specifications): void
    {
        $this->specifications = $specifications;
    }

    public function setIsActive(bool $is_active): void
    {
        $this->is_active = $is_active;
    }

    public function setCreatedBy(string $created_by): void
    {
        $this->created_by = $created_by;
    }

    // ========= CONVERSIÓN =========
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'category' => $this->category,
            'description' => $this->description,
            'specifications' => $this->specifications,
            'unit_price' => $this->unit_price,
            'is_active' => $this->is_active,
            'created_by' => $this->created_by,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'],
            $data['code'],
            $data['category'] ?? null,
            $data['description'] ?? null,
            $data['specifications'] ?? null,
            $data['unit_price'],
            $data['is_active'],
            $data['created_by']
        );
    }

    public static function fromModel($model): self
    {
        return new self(
            $model->id,
            $model->name,
            $model->code,
            $model->description,
            $model->price,
            $model->image,
            $model->stock,
            $model->min_stock,
            $model->is_active,
            $model->created_by
        );
    }
}
