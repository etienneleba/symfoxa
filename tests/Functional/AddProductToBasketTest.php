<?php

namespace App\Tests\Functional;

use App\Catalog\Application\Command\AddProductToBasket\AddProductToBasketCommand;
use App\Catalog\Application\Command\AddProductToBasket\AddProductToBasketCommandHandler;
use App\Catalog\Domain\Basket\BasketRepository;
use App\Catalog\Domain\Customer\CustomerNotFound;
use App\Catalog\Domain\Customer\CustomerRepository;
use App\Catalog\Domain\Product\ProductNotFound;
use App\Catalog\Domain\Product\ProductRepository;
use App\Catalog\Infrastructure\Persistence\InMemory\InMemoryBasketRepository;
use App\Catalog\Infrastructure\Persistence\InMemory\InMemoryCustomerRepository;
use App\Catalog\Infrastructure\Persistence\InMemory\InMemoryProductRepository;
use App\Common\RecordReplay\Generator;
use App\Common\RecordReplay\Mode;
use App\Common\RecordReplay\RecordReplayController;
use App\Common\RecordReplay\RecordReplayGenericDecorator;
use App\Tests\Builder\BasketBuilder;
use App\Tests\Builder\CustomerBuilder;
use App\Tests\Builder\ProductBuilder;
use PHPUnit\Framework\TestCase;

class AddProductToBasketTest extends TestCase
{
    private InMemoryBasketRepository|BasketRepository $inMemoryBasketRepository;
    private InMemoryProductRepository|ProductRepository $inMemoryProductRepository;
    private InMemoryCustomerRepository|CustomerRepository $inMemoryCustomerRepository;
    private RecordReplayController $recordReplayController;
    private Generator $generator;

    protected function setUp(): void {
        $this->recordReplayController  = new RecordReplayController();
        $this->recordReplayController->start("./tests/Functional/records/records_".$this->getName(). ".json", Mode::REPLAY);
        $this->generator = new Generator($this->recordReplayController);
        $this->inMemoryBasketRepository = $this->generator->createProxy(new InMemoryBasketRepository([]));
        $this->inMemoryProductRepository = $this->generator->createProxy(new InMemoryProductRepository([]));
        $this->inMemoryCustomerRepository = $this->generator->createProxy(new InMemoryCustomerRepository([]));
    }

    protected function tearDown(): void
    {
        $this->recordReplayController->save();
    }


    public function testAddAProductToTheBasket(): void
    {
        // Arrange
        $customerId = "1";
        $basketId = "1";
        $productId = "1";
        $this->givenAnEmptyBasketExists($basketId);
        $this->givenAProductExists($productId);
        $this->givenACustomerExists($customerId);

        // Act
        $this->whenIAddAProductToTheBasket(customerId: "1", basketId: $basketId, productId: $productId);

        // Assert
        $this->thenTheProductShouldBeInTheBasket($basketId, productId: "1");

    }

    public function testAddProductAlreadyInTheBasketToTheBasket(): void
    {
        // Arrange
        $customerId = "1";
        $productId = "1";
        $basketId = "1";
        $this->givenACustomerExists($customerId);
        $this->givenABasketWithOneProductExists($basketId, $productId);
        $this->givenAProductExists($productId);

        // Act
        $this->whenIAddAProductToTheBasket(customerId: $customerId, basketId: $basketId, productId: $productId);

        // Assert
        $this->thenTheProductShouldBeTwiceInTheBasket($basketId, $productId);
    }

    public function testAddProductThatDoesNotExistInTheBasket(): void {
        // Arrange
        $productId = "1";

        // Assert
        $this->thenIShouldGetAnError(ProductNotFound::class, "Product with id {$productId} not found");

        // Arrange
        $basketId = "1";
        $customerId = "1";

        $this->givenACustomerExists($customerId);
        $this->givenNoProductExists();
        $this->givenAnEmptyBasketExists($basketId);

        // Act
        $this->whenIAddAProductToTheBasket(customerId: $customerId, basketId: $basketId, productId:  $productId);

    }

    public function testAddProductToBasketWithCustomerNotExists(): void
    {
        // Arrange
        $customerId = "1";

        // Assert
        $this->thenIShouldGetAnError(CustomerNotFound::class, "Customer with id {$customerId} not found");

        // Arrange
        $productId = "1";
        $this->givenNoCustomerExists();
        $this->givenNoBasketExists();
        $this->givenAProductExists($productId);

        // Act
        $this->whenIAddAProductToTheBasket(customerId: "1", basketId: "1", productId: $productId);

    }



    /*
     * GIVEN
     */

    public function givenAnEmptyBasketExists(string $basketId): void
    {
        $basketSnapshot = (new BasketBuilder())
            ->setId($basketId)
            ->buildSnapshot();
        $this->inMemoryBasketRepository->setBasketSnapshots([$basketSnapshot->id => $basketSnapshot]);
    }

    private function givenABasketWithOneProductExists(string $basketId, string $productId)
    {
        $basketSnapshot = (new BasketBuilder())
            ->setId($basketId)
            ->setEntries([$productId => 1])
            ->buildSnapshot();
        $this->inMemoryBasketRepository->setBasketSnapshots([$basketSnapshot->id => $basketSnapshot]);
    }

    private function givenNoProductExists(): void
    {
        $this->inMemoryProductRepository->setProductSnapshots([]);
    }

    private function givenAProductExists(string $productId)
    {
        $productSnapshot = (new ProductBuilder())
            ->setId($productId)
            ->buildSnapshot();

        $this->inMemoryProductRepository->setProductSnapshots([$productSnapshot->id => $productSnapshot]);
    }


    private function givenNoCustomerExists(): void
    {
        $this->inMemoryCustomerRepository->setCustomerSnapshots([]);
    }

    private function givenNoBasketExists(): void
    {
        $this->inMemoryBasketRepository->setBasketSnapshots([]);
    }

    private function givenACustomerExists(string $customerId)
    {
        $customerSnapshot = (new CustomerBuilder())
            ->setId($customerId)
            ->buildSnapshot();
        $this->inMemoryCustomerRepository->setCustomerSnapshots([$customerSnapshot->id => $customerSnapshot]);
    }

    /*
     * WHEN
     */

    private function whenIAddAProductToTheBasket(string $customerId, string $basketId, string $productId): void
    {
        $commandHandler = new AddProductToBasketCommandHandler(
            $this->inMemoryBasketRepository,
            $this->inMemoryProductRepository,
            $this->inMemoryCustomerRepository
        );
        $commandHandler(new AddProductToBasketCommand(customerId: $customerId, basketId: $basketId, productId: $productId));
    }

    /*
     * THEN
     */

    private function thenTheProductShouldBeInTheBasket(string $basketId, string $productId): void
    {
        $expectedBasket = (new BasketBuilder())
            ->setEntries([$productId => 1])
            ->buildSnapshot();


        $this->assertEquals($expectedBasket, $this->inMemoryBasketRepository->getBasketSnapshots()[$basketId]);
    }

    private function thenTheProductShouldBeTwiceInTheBasket(string $basketId, string $productId): void
    {

        $expectedBasket = (new BasketBuilder())
            ->addEntry($productId, quantity: 2)
            ->buildSnapshot();

        $this->assertEquals($expectedBasket, $this->inMemoryBasketRepository->getBasketSnapshots()[$basketId]);
    }
    private function thenIShouldGetAnError(string $error, string $message): void
    {
        $this->expectException($error);
        $this->expectExceptionMessage($message);
    }



}
