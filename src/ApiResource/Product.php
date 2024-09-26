<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use App\Entity\Product\Product as ProductEntity;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/shop/products',
            normalizationContext: ['groups' => ['app:shop:product:index']],
            name: 'sylius_api_shop_product_get_collection',
        ),
    ],
)]
final class Product
{
    private string $anotherAdditionalInformation = 'Some another additional information';

    #[Groups(['app:shop:product:index', 'sylius:shop:product:index'])]
    public function getAnotherAdditionalInformation(): string
    {
        return $this->anotherAdditionalInformation;
    }

}
