<?php

namespace App\Catalog\Domain\Customer;

interface CustomerRepository
{

    public function exist(string $customerId): bool;
}
