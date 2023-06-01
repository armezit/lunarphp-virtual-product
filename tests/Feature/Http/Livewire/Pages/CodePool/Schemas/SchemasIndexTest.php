<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Feature\Http\Livewire\Pages\CodePool\Schemas;

use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Hub\Models\Staff;

/**
 * @group hub.products
 */
class SchemasIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cant_view_page_as_guest()
    {
        $this->get(route('hub.virtual-products.code-pool.schemas.index'))
            ->assertRedirect(route('hub.login'));
    }

    /** @test */
    public function cant_view_page_without_permission()
    {
        $staff = Staff::factory()->create([
            'admin' => false,
        ]);

        $this->actingAs($staff, 'staff');

        $this->get(route('hub.virtual-products.code-pool.schemas.index'))
            ->assertStatus(403);
    }

    /** @test */
    public function can_view_page_with_correct_permission()
    {
        $staff = Staff::factory()->create([
            'admin' => false,
        ]);

        $staff->permissions()->createMany([
            [
                'handle' => 'catalogue:manage-products',
            ],
        ]);

        $this->actingAs($staff, 'staff');

        $this->get(route('hub.virtual-products.code-pool.schemas.index'))
            ->assertSeeLivewire('hub.lunarphp-virtual-product.components.code_pool.schemas.index');
    }
}
