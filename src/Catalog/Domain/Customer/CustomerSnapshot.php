<?php

namespace App\Catalog\Domain\Customer;

readonly class CustomerSnapshot
{

    public function __construct(
        public string $id
    )
    {
    }
}
