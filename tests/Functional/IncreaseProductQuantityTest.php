<?php

namespace App\Tests\Functional;

use App\Catalog\Infrastructure\Persistence\InMemory\InMemoryBasketRepository;
use App\Catalog\Infrastructure\Persistence\InMemory\InMemoryCustomerRepository;
use App\Catalog\Infrastructure\Persistence\InMemory\InMemoryProductRepository;
use App\Tests\Builder\BasketBuilder;
use App\Tests\Builder\CustomerBuilder;
use App\Tests\Builder\ProductBuilder;
use PHPUnit\Framework\TestCase;

class IncreaseProductQuantityTest extends TestCase
{
    private InMemoryBasketRepository $inMemoryBasketRepository;
    private InMemoryProductRepository $inMemoryProductRepository;
    private InMemoryCustomerRepository $inMemoryCustomerRepository;

    protected function setUp(): void {
        $this->inMemoryBasketRepository = new InMemoryBasketRepository([]);
        $this->inMemoryProductRepository = new InMemoryProductRepository([]);
        $this->inMemoryCustomerRepository = new InMemoryCustomerRepository([]);
    }

    public function testIncreaseProductQuantity(): void
    {
        // Arrange
        $productId = "1";
        $productSnapshot = (new ProductBuilder())
            ->setId($productId)
            ->buildSnapshot();
        $basketId = "1";
        $basketSnapshot = (new BasketBuilder())
            ->setId("1")
            ->addEntry("1", 1)
            ->buildSnapshot();
        $customerId = "1";
        $customerSnapshot = (new CustomerBuilder())
            ->setId("1")
            ->buildSnapshot();

        $this->inMemoryProductRepository = new InMemoryProductRepository([$productSnapshot->id => $productSnapshot]);
        $this->inMemoryBasketRepository = new InMemoryBasketRepository([$basketSnapshot->id => $basketSnapshot]);
        $this->inMemoryCustomerRepository = new InMemoryCustomerRepository([$customerSnapshot->id => $customerSnapshot]);

        // Act
        $commandHandler = new IncreaseProductQuantityCommandHandler(
            $this->inMemoryBasketRepository,
            $this->inMemoryProductRepository,
            $this->inMemoryCustomerRepository
        );
        $commandHandler(new IncreaseProductQuantityCommand(customerId: $customerId, basketId: $basketId, productId: $productId));

        // Assert
        $expectedBasket = (new BasketBuilder())
            ->setEntries([$productId => 2])
            ->buildSnapshot();


        $this->assertEquals($expectedBasket, $this->inMemoryBasketRepository->basketSnapshots[$basketId]);
    }
}
