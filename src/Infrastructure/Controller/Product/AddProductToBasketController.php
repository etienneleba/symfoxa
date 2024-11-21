<?php

namespace App\Infrastructure\Controller\Product;

use Gazprom\Application\Command\Product\AddProductToBasket\AddProductToBasketCommand;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
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
