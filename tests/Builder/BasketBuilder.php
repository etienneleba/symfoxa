<?php

namespace App\Tests\Builder;

use App\Catalog\Domain\Basket\BasketSnapshot;

class BasketBuilder
{
    private string $id = "1";
    private string $customerId = "1";
    private array $entries = [];

    public function buildSnapshot(): BasketSnapshot
    {
        return new BasketSnapshot(
            $this->id,
            $this->customerId,
            $this->entries
        );
    }

    public function setId(string $id): BasketBuilder
    {
        $this->id = $id;

        return $this;
    }

    public function setCustomerId(string $customerId): BasketBuilder
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function setEntries(array $basketEntries): BasketBuilder
    {
        $this->entries = $basketEntries;

        return $this;
    }

    public function addEntry(string $productId, int $quantity): BasketBuilder
    {
        $this->entries[$productId] = $quantity;

        return $this;
    }


}
