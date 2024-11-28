<?php

namespace App\Catalog\Domain\Basket;

class Basket
{
    private array $entries = [];

    public function __construct(
        private readonly string $id,
        private readonly string $customerId,
    )
    {
    }

    public function add(string $productId): void
    {
        if(isset($this->entries[$productId])) {
            $this->entries[$productId]++;
        }
        else {
            $this->entries[$productId] = 1;
        }
    }

    public static function fromSnapshot(BasketSnapshot $snapshot): Basket
    {
        $basket = new Basket(
            $snapshot->id,
            $snapshot->customerId,
        );

        $basket->entries = $snapshot->entries;

        return $basket;


    }

    public function getSnapshot(): BasketSnapshot
    {
        return new BasketSnapshot(
            $this->id,
            $this->customerId,
            $this->entries
        );
    }
}
