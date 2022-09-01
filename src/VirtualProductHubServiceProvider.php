<?php

namespace Armezit\GetCandy\VirtualProduct;

use Armezit\GetCandy\VirtualProduct\Http\Livewire\Components\CodePool\ProductSettings;
use Armezit\GetCandy\VirtualProduct\Http\Livewire\Components\CodePool\Import;
use Armezit\GetCandy\VirtualProduct\Http\Livewire\Slots\VirtualProductSlot;
use GetCandy\Hub\Facades\Slot;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
        Livewire::component('hub.getcandy-virtual-product.slots.virtual-product-slot', VirtualProductSlot::class);

        Livewire::component('hub.getcandy-virtual-product.components.code_pool.product-settings', ProductSettings::class);
        Livewire::component('hub.getcandy-virtual-product.components.code_pool.import', Import::class);
    }

    protected function registerHubSlots()
    {
        if (config('getcandy-virtual-product.register_hub_slots', true)) {
            Slot::register(
                config('getcandy-virtual-product.virtual_product_slot', 'product.all'),
                VirtualProductSlot::class
            );
        }
    }
}
