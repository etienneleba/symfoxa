<?php

namespace App\Catalog\Application\Command\AddProductToBasket;

readonly class AddProductToBasketCommand
{

    public function __construct(
        public string $customerId,
        public string $basketId,
        public string $productId,
    )
    {
    }
}
