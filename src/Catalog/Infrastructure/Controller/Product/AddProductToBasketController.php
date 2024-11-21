<?php

namespace App\Catalog\Infrastructure\Controller\Product;

use App\Infrastructure\Controller\Product\CommandBus;
use Gazprom\Application\Command\Product\AddProductToBasket\AddProductToBasketCommand;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class AddProductToBasketController
{
    public function __construct(
        private CommandBus $commandBus
    )
    {
    }
    #[Route(path:"/add-product-to-basket" ,name: "add_product_to_basket_folder", methods: ["POST"])]
    public function __invoke(
        #[MapRequestPayload] AddProductToBasketCommand $command
    )
    {
        $this->commandBus->handle($command);
    }

}
