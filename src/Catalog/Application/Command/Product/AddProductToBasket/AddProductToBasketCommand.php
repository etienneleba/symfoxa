<?php

namespace App\Catalog\Application\Command\Product\AddProductToBasket;

readonly class AddProductToBasketCommand {

    public function __construct(
        public string $productId,
        public string $basketId,
    )
    {
    }
}