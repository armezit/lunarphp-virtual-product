<?php

namespace Armezit\Lunar\VirtualProduct;

use Armezit\Lunar\VirtualProduct\Commands\ListVirtualProducts;
use Armezit\Lunar\VirtualProduct\Lunar\Extensions\ProductResourceExtension;
use Lunar\Admin\Filament\Resources\ProductResource;
use Lunar\Admin\LunarPanelManager;
use Lunar\Hub\Http\Middleware\Authenticate;
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

        if (class_exists(Authenticate::class)) {
            $package->hasRoute('web');
        }
    }

    public function packageRegistered(): void
    {
        $this->app->afterResolving('lunar-panel', function (LunarPanelManager $lunarPanel): void {
            if (! class_exists(ProductResource::class)) {
                return;
            }

            $lunarPanel->extensions([
                ProductResource::class => ProductResourceExtension::class,
            ]);
        });
    }
}
