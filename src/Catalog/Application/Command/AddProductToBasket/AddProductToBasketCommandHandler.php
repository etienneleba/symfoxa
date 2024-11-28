<?php

namespace App\Catalog\Application\Command\AddProductToBasket;

use App\Catalog\Domain\Basket\BasketRepository;
use App\Catalog\Domain\Customer\CustomerNotFound;
use App\Catalog\Domain\Customer\CustomerRepository;
use App\Catalog\Domain\Product\ProductNotFound;
use App\Catalog\Domain\Product\ProductRepository;

class AddProductToBasketCommandHandler
{

    public function __construct(
        private BasketRepository $basketRepository,
        private ProductRepository $productRepository,
        private CustomerRepository $customerRepository
    )
    {
    }

    public function __invoke(AddProductToBasketCommand $command)
    {
        if(!$this->customerRepository->exist($command->customerId)) {
            throw new CustomerNotFound($command->customerId);
        }

        $basket = $this->basketRepository->get($command->basketId);

        if(!$this->productRepository->exist($command->productId)) {
            throw new ProductNotFound($command->productId);
        }

        $basket->add($command->productId);

        $this->basketRepository->save($basket);
    }


}
