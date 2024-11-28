<?php

namespace App\Tests\Builder;

use App\Catalog\Domain\Product\ProductSnapshot;

class ProductBuilder
{
    private $id = "1";
    private $name = "Guitar";

    public function __construct()
    {
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function buildSnapshot(): ProductSnapshot
    {
        return new ProductSnapshot(
            $this->id,
            $this->name,
        );
    }




}
