<?php

namespace App\Modules\Products\Domain\Entities;

class ProductEntity
{
    public function __construct(
        public ?string $id,
        public string $name,
        public string $code,
        public ?string $description,
        public float $price,
        public ?string $image,
        public int $stock,
        public int $min_stock,
        public bool $is_active,
        public string $created_by
    ) {}
}
