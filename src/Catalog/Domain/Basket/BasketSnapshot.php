<?php

namespace App\Catalog\Domain\Basket;

readonly class BasketSnapshot
{

    public function __construct(
        public string $id,
        public string $customerId,
        public array $entries
    )
    {
    }
}
