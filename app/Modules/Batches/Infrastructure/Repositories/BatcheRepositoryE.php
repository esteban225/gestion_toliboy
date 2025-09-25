<?php

namespace App\Modules\Batches\Infrastructure\Repositories;

use App\Models\Batch;
use App\Modules\Batches\Domain\Entities\BatcheEntity;
use App\Modules\Batches\Domain\Repositories\BatcheRepositoryI;

class BatcheRepositoryE implements BatcheRepositoryI
{
    public function all(array $filters = []): array
    {
        $query = Batch::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        $batches = $query->get();

        return array_map(fn ($batch) => $this->toEntity($batch), $batches->all());
    }

    public function find(string $id): ?BatcheEntity
    {
        $batch = Batch::find($id);

        return $batch ? $this->toEntity($batch) : null;
    }

    public function create(array $data): ?BatcheEntity
    {
        $batch = Batch::create($data);

        return $this->toEntity($batch);
    }

    public function update(array $data): bool
    {
        $batch = Batch::find($data['id']);
        if (! $batch) {
            return false;
        }

        return $batch->update($data);
    }

    public function delete(string $id): bool
    {
        $batch = Batch::find($id);
        if (! $batch) {
            return false;
        }

        return $batch->delete();
    }

    private function toEntity(Batch $batch): BatcheEntity
    {
        return new BatcheEntity(
            id: $batch->id,
            name: $batch->name,
            code: $batch->code,
            product_id: $batch->product_id,
            start_date: $batch->start_date,
            expected_end_date: $batch->expected_end_date,
            actual_end_date: $batch->actual_end_date,
            status: $batch->status,
            quantity: $batch->quantity,
            defect_quantity: $batch->defect_quantity,
            notes: $batch->notes,
            created_by: $batch->created_by
        );
    }
}
