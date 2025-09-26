<?php

namespace App\Modules\InventoryMovements\Domain\Entities;

/**
 * Entidad de dominio: Movimiento de Inventario (InvMoveEntitye).
 *
 * Representa un registro de movimiento de materia prima dentro del sistema.
 * - Responsabilidad: modelar los datos y reglas simples relacionados a un movimiento.
 * - No contiene lógica de persistencia ni dependencias de framework.
 *
 * Propiedades:
 *
 * @property int|null $id Identificador único del movimiento.
 * @property int $raw_material_id ID de la materia prima afectada.
 * @property int|null $batch_id ID del lote asociado (si aplica).
 * @property string $movement_type Tipo de movimiento (ej: "in", "out", "adjustment").
 * @property float $quantity Cantidad movida (positivo para entradas, negativo para salidas según convención).
 * @property float|null $unit_cost Costo unitario asociado al movimiento (opcional).
 * @property string|null $notes Notas adicionales del movimiento.
 * @property int|null $created_by ID del usuario que registró el movimiento.
 *
 * Ejemplos de uso:
 * - Validaciones y cálculos simples en servicios de dominio.
 * - Conversión a array para serialización en respuestas HTTP o exportación.
 */
class InvMoveEntity
{
    public function __construct(
        public ?int $id,
        public int $raw_material_id,
        public ?int $batch_id,
        public string $movement_type,
        public float $quantity,
        public ?float $unit_cost,
        public ?string $notes,
        public ?int $created_by,
    ) {}

    /**
     * Devuelve una representación en array de la entidad para serializar.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'raw_material_id' => $this->raw_material_id,
            'batch_id' => $this->batch_id,
            'movement_type' => $this->movement_type,
            'quantity' => $this->quantity,
            'unit_cost' => $this->unit_cost,
            'notes' => $this->notes,
            'created_by' => $this->created_by,
        ];
    }

    /**
     * Crea una instancia de InvMoveEntity a partir de un array asociativo.
     *
     * @param  array<string, mixed>  $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['raw_material_id'],
            $data['batch_id'] ?? null,
            $data['movement_type'],
            $data['quantity'],
            $data['unit_cost'] ?? null,
            $data['notes'] ?? null,
            $data['created_by'] ?? null,
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
