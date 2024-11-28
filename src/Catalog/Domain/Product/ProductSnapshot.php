<?php

namespace App\Catalog\Domain\Product;

readonly class ProductSnapshot
{

    public function __construct(
        public string $id,
        public string $name
    )
    {
    }
}
