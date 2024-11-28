<?php

namespace App\Catalog\Infrastructure\Persistence\InMemory;

use App\Catalog\Domain\Product\ProductRepository;

class InMemoryProductRepository implements ProductRepository
{

    public function __construct(
        private array $productSnapshots = []
    )
    {
    }

    public function exist(string $productId): bool
    {
        return array_key_exists($productId, $this->productSnapshots);
    }


}
