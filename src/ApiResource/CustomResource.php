<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\RandomProductProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/shop/custom-resources',
            name: 'app_api_custom_resource_get_collection',
            provider: RandomProductProvider::class,
        ),
    ],
)]
final class CustomResource
{
}
