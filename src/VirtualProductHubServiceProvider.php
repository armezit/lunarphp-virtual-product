<?php

namespace Armezit\Lunar\VirtualProduct;

use Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Import;
use Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\ProductSettings;
use Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Schemas\SchemaCreate;
use Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Schemas\SchemaShow;
use Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Schemas\SchemasIndex;
use Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Schemas\SchemasTable;
use Armezit\Lunar\VirtualProduct\Http\Livewire\Slots\VirtualProductSlot;
use Armezit\Lunar\VirtualProduct\Tables\Builders\CodePoolSchemasTableBuilder;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Lunar\Hub\Facades\Menu;
use Lunar\Hub\Facades\Slot;
use Lunar\Hub\Menu\MenuLink;

class VirtualProductHubServiceProvider extends ServiceProvider
{
    /**
     * Boot up the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerLivewireComponents();
        $this->registerHubSlots();
        $this->registerMenu();
    }

    /**
     * Register the hub's Livewire components.
     *
     * @return void
     */
    protected function registerLivewireComponents()
    {
        Livewire::component('hub.lunarphp-virtual-product.slots.virtual-product-slot', VirtualProductSlot::class);

        Livewire::component('hub.lunarphp-virtual-product.components.code_pool.schemas.index', SchemasIndex::class);
        Livewire::component('hub.lunarphp-virtual-product.components.code_pool.schemas.table', SchemasTable::class);
        Livewire::component('hub.lunarphp-virtual-product.components.code_pool.schemas.create', SchemaCreate::class);
        Livewire::component('hub.lunarphp-virtual-product.components.code_pool.schemas.show', SchemaShow::class);

        Livewire::component('hub.lunarphp-virtual-product.components.code_pool.product-settings', ProductSettings::class);
        Livewire::component('hub.lunarphp-virtual-product.components.code_pool.import', Import::class);
    }

    protected function registerHubSlots()
    {
        if (config('lunarphp-virtual-product.register_hub_slots', true)) {
            Slot::register(
                config('lunarphp-virtual-product.virtual_product_slot', 'product.all'),
                VirtualProductSlot::class
            );
        }
    }

    protected function registerMenu()
    {
        /** @var \Lunar\Hub\Menu\MenuSlot $sidebarSlot */
        $sidebarSlot = Menu::slot('sidebar');

        $catalogueGroup = $sidebarSlot->group('hub.catalogue');

        $virtualProductGroup = $catalogueGroup
            ->section('hub.virtual-products')
            ->name(__('lunarphp-virtual-product::default.virtual-product'))
            ->handle('hub.virtual-products')
            ->route('hub.virtual-products.index')
            ->icon('cube-transparent');

        $virtualProductGroup->addItem(function (MenuLink $item) {
            $item->name(__('lunarphp-virtual-product::code-pool.pages.schemas.index.title'))
                ->handle('hub.virtual-products.code-pool.schemas.index')
                ->route('hub.virtual-products.code-pool.schemas.index')
                ->gate('settings:core')
                ->icon('cube-transparent');
        });

        $virtualProductGroup->addItem(function (MenuLink $item) {
            $item->name(__('lunarphp-virtual-product::code-pool.pages.import.title'))
                ->handle('hub.virtual-products.code-pool.import')
                ->route('hub.virtual-products.code-pool.import')
                ->gate('settings:core')
                ->icon('cloud-upload');
        });
    }

    protected function registerTableBuilders()
    {
        $tableBuilders = [
            CodePoolSchemasTableBuilder::class,
        ];

        foreach ($tableBuilders as $tableBuilder) {
            $this->app->singleton($tableBuilder, function ($app) use ($tableBuilder) {
                return new $tableBuilder();
            });
        }
    }
}
