<?php

namespace App\Modules\Batches\Domain\Entities;

use App\Models\Batch; // Asegúrate de importar el modelo correcto

class BatcheEntity
{
    private ?int $id;

    private string $name;

    private string $code;

    private ?int $product_id;

    private ?string $start_date;

    private ?string $expected_end_date;

    private ?string $actual_end_date;

    private string $status;

    private ?int $quantity;

    private ?int $defect_quantity;

    private ?string $notes;

    private int $created_by;

    public function __construct(
        ?int $id,
        string $name,
        string $code,
        ?int $product_id,
        ?string $start_date,
        ?string $expected_end_date,
        ?string $actual_end_date,
        string $status,
        ?int $quantity,
        ?int $defect_quantity,
        ?string $notes,
        int $created_by
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->product_id = $product_id;
        $this->start_date = $start_date;
        $this->expected_end_date = $expected_end_date;
        $this->actual_end_date = $actual_end_date;
        $this->status = $status;
        $this->quantity = $quantity;
        $this->defect_quantity = $defect_quantity;
        $this->notes = $notes;
        $this->created_by = $created_by;
    }

    // ============================
    // Getters y Setters
    // ============================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getProductId(): ?int
    {
        return $this->product_id;
    }

    public function setProductId(?int $product_id): void
    {
        $this->product_id = $product_id;
    }

    public function getStartDate(): ?string
    {
        return $this->start_date;
    }

    public function setStartDate(?string $start_date): void
    {
        $this->start_date = $start_date;
    }

    public function getExpectedEndDate(): ?string
    {
        return $this->expected_end_date;
    }

    public function setExpectedEndDate(?string $expected_end_date): void
    {
        $this->expected_end_date = $expected_end_date;
    }

    public function getActualEndDate(): ?string
    {
        return $this->actual_end_date;
    }

    public function setActualEndDate(?string $actual_end_date): void
    {
        $this->actual_end_date = $actual_end_date;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getDefectQuantity(): ?int
    {
        return $this->defect_quantity;
    }

    public function setDefectQuantity(?int $defect_quantity): void
    {
        $this->defect_quantity = $defect_quantity;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function getCreatedBy(): int
    {
        return $this->created_by;
    }

    public function setCreatedBy(int $created_by): void
    {
        $this->created_by = $created_by;
    }

    // ============================
    // Métodos estáticos de ayuda
    // ============================

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'],
            $data['code'],
            $data['product_id'] ?? null,
            $data['start_date'] ?? null,
            $data['expected_end_date'] ?? null,
            $data['actual_end_date'] ?? null,
            $data['status'],
            $data['quantity'] ?? null,
            $data['defect_quantity'] ?? null,
            $data['notes'] ?? null,
            $data['created_by']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'product_id' => $this->product_id,
            'start_date' => $this->start_date,
            'expected_end_date' => $this->expected_end_date,
            'actual_end_date' => $this->actual_end_date,
            'status' => $this->status,
            'quantity' => $this->quantity,
            'defect_quantity' => $this->defect_quantity,
            'notes' => $this->notes,
            'created_by' => $this->created_by,
        ];
    }

    public static function fromModel(Batch $model): self
    {
        return new self(
            $model->id,
            $model->name,
            $model->code,
            $model->product_id,
            $model->start_date,
            $model->expected_end_date,
            $model->actual_end_date,
            $model->status,
            $model->quantity,
            $model->defect_quantity,
            $model->notes,
            $model->created_by
        );
    }
}
