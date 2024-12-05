<?php

namespace App\Catalog\Infrastructure\Persistence\InMemory;

use App\Catalog\Domain\Basket\Basket;
use App\Catalog\Domain\Basket\BasketRepository;

class InMemoryBasketRepository implements BasketRepository
{

    public function __construct(
        public array $basketSnapshots
    )
    {
    }

    public function get(string $basketId): Basket
    {
        foreach ($this->basketSnapshots as $basketSnapshot) {
            if($basketSnapshot->id === $basketId) {
                return Basket::fromSnapshot($basketSnapshot);
            }
        }
    }

    public function save(Basket $basket): void
    {
        $snapshot = $basket->toSnapshot();
        $this->basketSnapshots[$snapshot->id] = $snapshot;
    }

}
