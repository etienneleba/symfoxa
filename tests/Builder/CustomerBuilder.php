<?php

namespace App\Tests\Builder;

use App\Catalog\Domain\Customer\CustomerSnapshot;

class CustomerBuilder
{

    private string $id;
    public function __construct()
    {
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function buildSnapshot(): CustomerSnapshot
    {
        return new CustomerSnapshot(
            $this->id,
        );
    }


}
