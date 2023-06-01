<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Unit\Http\Livewire\Components\CodePool\Schemas;

use Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Schemas\SchemaCreate;
use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Lunar\Hub\Models\Staff;

class SchemaCreateTest extends TestCase
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
            ->test(SchemaCreate::class)
            ->assertViewIs('lunarphp-virtual-product::livewire.components.code-pool.schemas.create');
    }

}
