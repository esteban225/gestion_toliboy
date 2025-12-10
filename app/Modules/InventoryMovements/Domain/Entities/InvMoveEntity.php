<?php

namespace App\Modules\InventoryMovements\Domain\Entities;

/**
 * Entidad de dominio: Movimiento de Inventario (InvMoveEntity).
 */
class InvMoveEntity
{
    private ?int $id;

    private int $raw_material_id;

    private ?int $batch_id;

    private string $movement_type;

    private string $production_line;

    private float $quantity;

    private ?float $unit_cost;

    private ?string $notes;

    private ?int $created_by;

    public function __construct(
        ?int $id,
        int $raw_material_id,
        ?int $batch_id,
        string $movement_type,
        string $production_line,
        float $quantity,
        ?float $unit_cost,
        ?string $notes,
        ?int $created_by,
    ) {
        $this->id = $id;
        $this->raw_material_id = $raw_material_id;
        $this->batch_id = $batch_id;
        $this->movement_type = $movement_type;
        $this->production_line = $production_line;
        $this->quantity = $quantity;
        $this->unit_cost = $unit_cost;
        $this->notes = $notes;
        $this->created_by = $created_by;
    }

    // -------- Getters --------
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRawMaterialId(): int
    {
        return $this->raw_material_id;
    }

    public function getBatchId(): ?int
    {
        return $this->batch_id;
    }

    public function getMovementType(): string
    {
        return $this->movement_type;
    }

    public function getProductionLine(): string
    {
        return $this->production_line;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function getUnitCost(): ?float
    {
        return $this->unit_cost;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function getCreatedBy(): ?int
    {
        return $this->created_by;
    }

    // -------- Setters --------
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setRawMaterialId(int $raw_material_id): void
    {
        $this->raw_material_id = $raw_material_id;
    }

    public function setBatchId(?int $batch_id): void
    {
        $this->batch_id = $batch_id;
    }

    public function setMovementType(string $movement_type): void
    {
        $this->movement_type = $movement_type;
    }

    public function setProductionLine(string $production_line): void
    {
        $this->production_line = $production_line;
    }

    public function setQuantity(float $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function setUnitCost(?float $unit_cost): void
    {
        $this->unit_cost = $unit_cost;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function setCreatedBy(?int $created_by): void
    {
        $this->created_by = $created_by;
    }

    /**
     * Devuelve una representación en array de la entidad.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'raw_material_id' => $this->raw_material_id,
            'batch_id' => $this->batch_id,
            'movement_type' => $this->movement_type,
            'production_line' => $this->production_line,
            'quantity' => $this->quantity,
            'unit_cost' => $this->unit_cost,
            'notes' => $this->notes,
            'created_by' => $this->created_by,
        ];
    }

    /**
     * Crea una instancia desde un array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['raw_material_id'],
            $data['batch_id'] ?? null,
            $data['movement_type'],
            $data['production_line'],
            (float) $data['quantity'],
            isset($data['unit_cost']) ? (float) $data['unit_cost'] : null,
            $data['notes'] ?? null,
            $data['created_by'] ?? null,
        );
    }

    public static function fromModel($model): ?self
    {
        if (! $model) {
            return null;
        }

        return new self(
            $model->id,
            $model->raw_material_id,
            $model->batch_id,
            $model->movement_type,
            $model->production_line,
            (float) $model->quantity,
            isset($model->unit_cost) ? (float) $model->unit_cost : null,
            $model->notes,
            $model->created_by,
        );
    }

    /**
     * Indica si el movimiento es de entrada.
     */
    public function isInbound(): bool
    {
        return strtolower($this->movement_type) === 'in';
    }

    /**
     * Indica si el movimiento es de salida.
     */
    public function isOutbound(): bool
    {
        return strtolower($this->movement_type) === 'out';
    }
}
