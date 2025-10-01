<?php

namespace App\Modules\RawMaterials\Domain\Entities;

/**
 * Entidad de dominio para materia prima.
 *
 * Representa los datos y reglas de negocio de una materia prima en el sistema de producción.
 * Esta entidad pura no depende de ningún framework y encapsula la lógica del dominio.
 *
 * Principios SOLID aplicados:
 * - SRP: Solo gestiona datos y comportamientos de materia prima.
 * - OCP: Puede extenderse sin modificar el código base.
 * - LSP: Subclases pueden sustituir esta clase sin romper el sistema.
 * - ISP: Si implementa interfaces, deben ser específicas.
 * - DIP: Puede depender de abstracciones para mayor flexibilidad.
 */
class RawMaterialEntity
{
    private ?int $id;

    private string $name;

    private string $code;

    private ?string $description;

    private string $unit_of_measure;

    private float $stock;

    private float $min_stock;

    private float $is_active; // 1.0 activo, 0.0 inactivo

    private ?int $created_by;

    /**
     * Crea una nueva instancia de materia prima.
     */
    public function __construct(
        ?int $id,
        string $name,
        string $code,
        ?string $description,
        string $unit_of_measure,
        float $stock,
        float $min_stock,
        float $is_active,
        ?int $created_by
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->description = $description;
        $this->unit_of_measure = $unit_of_measure;
        $this->stock = $stock;
        $this->min_stock = $min_stock;
        $this->is_active = $is_active;
        $this->created_by = $created_by;
    }

    // ========= GETTERS =========
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

    public function getUnitOfMeasure(): string
    {
        return $this->unit_of_measure;
    }

    public function getStock(): float
    {
        return $this->stock;
    }

    public function getMinStock(): float
    {
        return $this->min_stock;
    }

    public function getIsActive(): float
    {
        return $this->is_active;
    }

    public function getCreatedBy(): ?int
    {
        return $this->created_by;
    }

    // ========= SETTERS =========
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

    public function setUnitOfMeasure(string $unit_of_measure): void
    {
        $this->unit_of_measure = $unit_of_measure;
    }

    public function setStock(float $stock): void
    {
        $this->stock = $stock;
    }

    public function setMinStock(float $min_stock): void
    {
        $this->min_stock = $min_stock;
    }

    public function setIsActive(float $is_active): void
    {
        $this->is_active = $is_active;
    }

    public function setCreatedBy(?int $created_by): void
    {
        $this->created_by = $created_by;
    }

    // ========= REGLAS DE NEGOCIO =========
    public function isActive(): bool
    {
        return $this->is_active === 1.0;
    }

    public function requiresRestock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    public function getRestockQuantity(): float
    {
        return $this->requiresRestock() ? ($this->min_stock - $this->stock) : 0.0;
    }

    // ========= CONVERSIÓN =========
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'unit_of_measure' => $this->unit_of_measure,
            'stock' => $this->stock,
            'min_stock' => $this->min_stock,
            'is_active' => $this->is_active,
            'created_by' => $this->created_by,
        ];
    }

    /**
     * Crea una nueva instancia de RawMaterialEntity a partir de un arreglo asociativo.
     *
     * Este método de fábrica estático asigna los datos proporcionados en el arreglo a las propiedades de la entidad.
     * Espera claves como 'id', 'name', 'code', 'description', 'unit_of_measure',
     * 'stock', 'min_stock', 'is_active' y 'created_by'. Los campos opcionales ('id', 'description',
     * 'created_by') tendrán el valor null si no están presentes en el arreglo.
     *
     * @param  array  $data  Arreglo asociativo que contiene los datos de la materia prima.
     * @return self Retorna una nueva instancia de RawMaterialEntity poblada con los datos proporcionados.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'],
            $data['code'],
            $data['description'] ?? null,
            $data['unit_of_measure'],
            $data['stock'],
            $data['min_stock'],
            $data['is_active'],
            $data['created_by'] ?? null
        );
    }

    public static function fromModel($model): ?self
    {
        if (! $model) {
            return null;
        }

        return new self(
            $model->id,
            $model->name,
            $model->code,
            $model->description,
            $model->unit_of_measure,
            (float) $model->stock,
            (float) $model->min_stock,
            (float) $model->is_active,
            $model->created_by
        );
    }
}
