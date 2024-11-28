<?php

namespace App\Catalog\Domain\Product;

interface ProductRepository
{

    public function exist(string $productId): bool;
}
