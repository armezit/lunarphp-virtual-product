<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Unit\Http\Livewire\Slots;

use Armezit\Lunar\VirtualProduct\Http\Livewire\Slots\VirtualProductSlot;
use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Lunar\Hub\Models\Staff;

class VirtualProductSlotTest extends TestCase
{
    use RefreshDatabase;

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
}
