<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Unit\Http\Livewire\Slots;

use Armezit\Lunar\VirtualProduct\Http\Livewire\Slots\VirtualProductSlot;
use Armezit\Lunar\VirtualProduct\Models\VirtualProduct;
use Armezit\Lunar\VirtualProduct\SourceProviders\CodePool;
use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Lunar\Hub\Models\Staff;
use Lunar\Models\Product;

class VirtualProductSlotTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function component_mounts_correctly()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        LiveWire::actingAs($staff, 'staff')
            ->test(VirtualProductSlot::class)
            ->assertViewIs('lunarphp-virtual-product::livewire.slots.virtual-product');
    }

    /** @test */
    public function component_mounts_correctly_with_initial_product()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        $product = Product::factory()->create([
            'id' => $this->faker->numberBetween(1, 1000),
        ]);

        LiveWire::actingAs($staff, 'staff')
            ->test(VirtualProductSlot::class, ['slotModel' => $product])
            ->assertSet('enabled', false)
            ->assertCount('sources', 1)
            ->assertSet('sources.0.enabled', false);

        VirtualProduct::factory()->create([
            'product_id' => $product->id,
            'source' => CodePool::class,
            'meta' => ['schemaId' => 1],
        ]);

        LiveWire::actingAs($staff, 'staff')
            ->test(VirtualProductSlot::class, ['slotModel' => $product])
            ->assertSet('enabled', true)
            ->assertCount('sources', 1)
            ->assertSet('sources.0.enabled', true);
    }
}
