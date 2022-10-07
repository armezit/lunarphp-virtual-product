<?php

namespace Armezit\Lunar\VirtualProduct;

use Armezit\Lunar\VirtualProduct\Commands\ListVirtualProducts;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class VirtualProductServiceProvider extends PackageServiceProvider
{
    public static string $name = 'lunarphp-virtual-product';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(self::$name)
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasRoute('web')
            ->hasMigrations([
                'create_virtual_products_table',
                'create_virtual_products_code_pool_archive_table',
                'create_virtual_products_code_pool_batches_table',
                'create_virtual_products_code_pool_items_table',
                'create_virtual_products_code_pool_schema_table',
            ])
            ->runsMigrations()
            ->hasCommands([
                ListVirtualProducts::class,
            ]);
    }
}
