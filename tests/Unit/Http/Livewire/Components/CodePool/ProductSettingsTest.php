<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Unit\Http\Livewire\Components\CodePool;

use Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\ProductSettings;
use Armezit\Lunar\VirtualProduct\Models\VirtualProduct;
use Armezit\Lunar\VirtualProduct\SourceProviders\CodePool;
use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Lunar\Hub\Models\Staff;
use Lunar\Models\Product;

class ProductSettingsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function component_mounts_correctly()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        $product = Product::factory()->create();

        VirtualProduct::factory()->create([
            'product_id' => $product->id,
            'source' => CodePool::class,
            'meta' => ['schemaId' => 65],
        ]);

        LiveWire::actingAs($staff, 'staff')
            ->test(ProductSettings::class, ['product' => $product])
            ->assertViewIs('lunarphp-virtual-product::livewire.components.code-pool.product-settings')
            ->assertSet('schemaId', 65);
    }
}
