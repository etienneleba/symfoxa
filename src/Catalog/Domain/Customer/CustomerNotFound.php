<?php

namespace App\Catalog\Domain\Customer;

class CustomerNotFound extends \Exception
{


    public function __construct(string $customerId)
    {
        parent::__construct("Customer with id {$customerId} not found");
    }
}
