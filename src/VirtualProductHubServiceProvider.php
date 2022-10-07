<?php

namespace Armezit\Lunar\VirtualProduct;

use Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Import;
use Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\ProductSettings;
use Armezit\Lunar\VirtualProduct\Http\Livewire\Slots\VirtualProductSlot;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Lunar\Hub\Facades\Slot;

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
    }

    /**
     * Register the hub's Livewire components.
     *
     * @return void
     */
    protected function registerLivewireComponents()
    {
        Livewire::component('hub.lunarphp-virtual-product.slots.virtual-product-slot', VirtualProductSlot::class);

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
}
