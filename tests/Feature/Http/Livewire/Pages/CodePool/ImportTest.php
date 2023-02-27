<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Feature\Http\Livewire\Pages\CodePool;

use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Hub\Models\Staff;

/**
 * @group hub.products
 */
class ImportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cant_view_page_as_guest()
    {
        $this->get(route('hub.virtual-products.code-pool.import'))
            ->assertRedirect(route('hub.login'));
    }

    /** @test */
    public function cant_view_page_without_permission()
    {
        $staff = Staff::factory()->create([
            'admin' => false,
        ]);

        $this->actingAs($staff, 'staff');

        $this->get(route('hub.virtual-products.code-pool.import'))
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

        $this->get(route('hub.virtual-products.code-pool.import'))
            ->assertSeeLivewire('hub.lunarphp-virtual-product.components.code_pool.import');
    }
}
