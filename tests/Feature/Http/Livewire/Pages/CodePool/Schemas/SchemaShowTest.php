<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Feature\Http\Livewire\Pages\CodePool\Schemas;

use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Hub\Models\Staff;

/**
 * @group hub.products
 */
class SchemaShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cant_view_page_as_guest()
    {
        $this->get(route('hub.virtual-products.code-pool.schemas.show', ['schema' => 1]))
            ->assertRedirect(route('hub.login'));
    }

    /** @test */
    public function cant_view_page_without_permission()
    {
        $staff = Staff::factory()->create([
            'admin' => false,
        ]);

        $this->actingAs($staff, 'staff');

        $this->get(route('hub.virtual-products.code-pool.schemas.show', ['schema' => 1]))
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

        $codePoolSchema = CodePoolSchema::factory()->create();

        $this->get(route('hub.virtual-products.code-pool.schemas.show', ['schema' => $codePoolSchema->id]))
            ->assertSeeLivewire('hub.lunarphp-virtual-product.components.code_pool.schemas.show');
    }
}
