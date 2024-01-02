<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Unit\Http\Livewire\Components\CodePool\Schemas;

use Armezit\Lunar\VirtualProduct\Data\CodePoolSchemaField;
use Armezit\Lunar\VirtualProduct\Data\CodePoolSchemaFieldsList;
use Armezit\Lunar\VirtualProduct\Enums\CodePoolFieldType;
use Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Schemas\SchemaCreate;
use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
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

    /** @test */
    public function can_add_field_correctly()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        $fields = [
            ['name' => 'int-field', 'type' => CodePoolFieldType::Integer->value],
            ['name' => 'raw-field', 'type' => CodePoolFieldType::Raw->value],
        ];

        LiveWire::actingAs($staff, 'staff')
            ->test(SchemaCreate::class)
            ->call('addField', $fields[0])
            ->call('addField', $fields[1])
            ->assertSet('fields', new CodePoolSchemaFieldsList(
                fields: CodePoolSchemaField::collection(array_merge($fields, [['order' => 1], ['order' => 2]]))
            ));
    }

    /** @test */
    public function can_create_schema()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        LiveWire::actingAs($staff, 'staff')
            ->test(SchemaCreate::class)
            ->call('addField', [
                'name' => 'int-field',
                'type' => CodePoolFieldType::Integer,
            ])
            ->call('addField', [
                'name' => 'raw-field',
                'type' => CodePoolFieldType::Raw,
            ])
            ->assertSet('fields', 7);
    }
}
