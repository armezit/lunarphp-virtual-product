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

    public function setUp(): void
    {
        parent::setUp();

        $staff = Staff::factory()->create([
            'admin' => true,
        ]);
        $this->actingAs($staff, 'staff');
    }

    private function getCodePoolSchemaTable(): string
    {
        return (new CodePoolSchema)->getTable();
    }

    /** @test */
    public function component_mounts_correctly()
    {
        LiveWire::test(SchemaCreate::class)
            ->assertViewIs('lunarphp-virtual-product::livewire.components.code-pool.schemas.create');
    }

    /** @test */
    public function schema_name_is_required()
    {
        Livewire::test(SchemaCreate::class)
            ->set('schema.name', '')
            ->call('save')
            ->assertHasErrors(['schema.name' => 'required']);
    }

    /** @test */
    public function schema_fields_validation_is_correct()
    {
        Livewire::test(SchemaCreate::class)
            ->set('fields', new CodePoolSchemaFieldsList(
                fields: CodePoolSchemaField::collection([])
            ))
            ->call('save')
            ->assertHasErrors(['fields' => 'required']);

        Livewire::test(SchemaCreate::class)
            ->set('fields', new CodePoolSchemaFieldsList(
                fields: CodePoolSchemaField::collection([
                    [
                        'name' => '',
                        'type' => CodePoolFieldType::Raw->value,
                        'order' => 1,
                    ],
                ])
            ))
            ->call('save')
            ->assertHasErrors(['fields.0.name' => 'required']);
    }

    /** @test */
    public function can_add_fields_correctly()
    {
        $fields = [
            ['name' => 'int-field', 'type' => CodePoolFieldType::Integer->value, 'order' => 1],
            ['name' => 'raw-field', 'type' => CodePoolFieldType::Raw->value, 'order' => 2],
        ];

        LiveWire::test(SchemaCreate::class)
            ->set('fields', new CodePoolSchemaFieldsList(
                fields: CodePoolSchemaField::collection([])
            ))
            ->call('addField', ...$fields[0])
            ->call('addField', ...$fields[1])
            ->assertCount('fields', 2);
    }

    /** @test */
    public function can_create_schema()
    {
        $fields = [
            ['name' => 'int-field', 'type' => CodePoolFieldType::Integer->value, 'order' => 1],
            ['name' => 'raw-field', 'type' => CodePoolFieldType::Raw->value, 'order' => 2],
        ];

        LiveWire::test(SchemaCreate::class)
            ->set('schema.name', 'Test Schema')
            ->set('fields', new CodePoolSchemaFieldsList(
                fields: CodePoolSchemaField::collection([])
            ))
            ->call('addField', ...$fields[0])
            ->call('addField', ...$fields[1])
            ->call('save');

        $this->assertDatabaseHas($this->getCodePoolSchemaTable(), [
            'name' => 'Test Schema',
            'fields' => $this->castAsJson($fields),
        ]);
    }
}
