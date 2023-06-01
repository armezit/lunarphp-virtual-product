<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Unit\Http\Livewire\Components\CodePool\Schemas;

use Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Schemas\SchemaShow;
use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Lunar\Hub\Models\Staff;

class SchemaShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function component_mounts_correctly()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        $codePoolSchema = CodePoolSchema::factory()->create();

        LiveWire::actingAs($staff, 'staff')
            ->test(SchemaShow::class, ['schema' => $codePoolSchema])
            ->assertViewIs('lunarphp-virtual-product::livewire.components.code-pool.schemas.show');
    }

}
