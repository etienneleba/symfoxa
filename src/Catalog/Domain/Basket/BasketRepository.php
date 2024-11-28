<?php

namespace App\Catalog\Domain\Basket;

interface BasketRepository
{
    public function get(string $basketId): Basket;

    public function save(Basket $basket): void;


}
