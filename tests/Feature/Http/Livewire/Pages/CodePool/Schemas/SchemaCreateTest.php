<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Feature\Http\Livewire\Pages\CodePool\Schemas;

use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Hub\Models\Staff;

/**
 * @group hub.products
 */
class SchemaCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cant_view_page_as_guest()
    {
        $this->get(route('hub.virtual-products.code-pool.schemas.create'))
            ->assertRedirect(route('hub.login'));
    }

    /** @test */
    public function cant_view_page_without_permission()
    {
        $this->setupRolesPermissions();

        $staff = Staff::factory()->create([
            'admin' => false,
        ]);

        $this->actingAs($staff, 'staff');

        $this->get(route('hub.virtual-products.code-pool.schemas.create'))
            ->assertStatus(403);
    }

    /** @test */
    public function can_view_page_with_correct_permission()
    {
        $this->setupRolesPermissions();

        $staff = Staff::factory()->create([
            'admin' => false,
        ]);

        $staff->givePermissionTo('catalogue:manage-products');

        $this->actingAs($staff, 'staff');

        $this->get(route('hub.virtual-products.code-pool.schemas.create'))
            ->assertSeeLivewire('hub.lunarphp-virtual-product.components.code_pool.schemas.create');
    }
}
