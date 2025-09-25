<?php

namespace App\Modules\Batches\Domain\Entities;

class BatcheEntity
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $code,
        public ?int $product_id,
        public ?string $start_date,
        public ?string $expected_end_date,
        public ?string $actual_end_date,
        public string $status,
        public ?int $quantity,
        public ?int $defect_quantity,
        public ?string $notes,
        public int $created_by
    ) {}
}
