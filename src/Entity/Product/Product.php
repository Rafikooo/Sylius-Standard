<?php

declare(strict_types=1);

namespace App\Entity\Product;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Product as BaseProduct;
use Sylius\Component\Product\Model\ProductTranslationInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/shop/products',
            normalizationContext: ['groups' => ['app:shop:product:index']],
            name: 'sylius_api_shop_product_get_collection',
        ),
    ],
)]
#[ORM\Entity]
#[ORM\Table(name: 'sylius_product')]
class Product extends BaseProduct
{
    private string $additionalInformation = 'Some additional information';

    #[Groups(['app:shop:product:index', 'sylius:shop:product:index'])]
    public function getAdditionalInformation(): string
    {
        return $this->additionalInformation;
    }

    protected function createTranslation(): ProductTranslationInterface
    {
        return new ProductTranslation();
    }
}
