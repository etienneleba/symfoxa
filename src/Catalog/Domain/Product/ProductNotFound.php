<?php

namespace App\Catalog\Domain\Product;

class ProductNotFound extends \Exception
{

    public function __construct(string $productId)
    {
        parent::__construct("Product with id {$productId} not found");
    }
}
