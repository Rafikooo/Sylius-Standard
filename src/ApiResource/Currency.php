<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\RandomProductProvider;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/shop/currencies',
            normalizationContext: ['groups' => ['app:shop:currency:index']],
            name: 'sylius_api_shop_currency_get_collection',
            provider: RandomProductProvider::class,
        ),
    ],
)]
final class Currency
{
    private string $currencyDescription = 'Some currency description';

    #[Groups(['app:shop:currency:index', 'sylius:shop:currency:index'])]
    public function getCurrencyDescription(): string
    {
        return $this->currencyDescription;
    }
}
