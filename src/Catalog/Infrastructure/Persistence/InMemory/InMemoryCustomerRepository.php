<?php

namespace App\Catalog\Infrastructure\Persistence\InMemory;

use App\Catalog\Domain\Customer\CustomerRepository;

class InMemoryCustomerRepository implements CustomerRepository
{

    public function __construct(private array $customerSnapshots = [])
    {
    }

    public function exist(string $customerId): bool
    {
        return array_key_exists($customerId, $this->customerSnapshots);
    }

    public function setCustomerSnapshots(array $customerSnapshots): void
    {
        $this->customerSnapshots = $customerSnapshots;
    }


}
