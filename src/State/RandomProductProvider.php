<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;

readonly class RandomProductProvider implements ProviderInterface
{
    public function __construct(private ProductRepositoryInterface $productRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $products = $this->productRepository->findAll();

        return $products[array_rand($products)];
    }
}
