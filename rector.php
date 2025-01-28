<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Sylius\SyliusRector\Set\SyliusMarketplace;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src'])
    ->withSets([SyliusMarketplace::MARKETPLACE_PLUGIN])
    ->withImportNames(removeUnusedImports: true)
;
