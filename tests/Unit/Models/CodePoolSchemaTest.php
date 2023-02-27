<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Unit\Models;

use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;

class CodePoolSchemaTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private function getCodePoolSchemaTable(): string
    {
        return (new CodePoolSchema)->getTable();
    }

    /** @test */
    public function can_make_a_code_pool_schema()
    {
        $fields = [
            ['name' => $this->faker->word, 'type' => $this->faker->word, 'order' => 1],
            ['name' => $this->faker->word, 'type' => $this->faker->word, 'order' => 2],
        ];
        $data = [
            'name' => $this->faker->word,
        ];
        CodePoolSchema::factory()->create(array_merge($data, ['fields' => $fields]));

        $this->assertDatabaseHas($this->getCodePoolSchemaTable(), array_merge($data, [
            'fields' => $this->castAsJson($fields),
        ]));
    }

    /** @test */
    public function code_pool_schema_has_correct_casting()
    {
        $codePoolSchema = CodePoolSchema::factory()->create();

        $this->assertInstanceOf(Collection::class, $codePoolSchema->fields);

        $firstField = $codePoolSchema->fields->first();
        $this->assertIsString($firstField['name']);
        $this->assertIsInt($firstField['order']);
    }
}
