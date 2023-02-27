<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Unit\Data;

use Armezit\Lunar\VirtualProduct\Data\CodePoolSchemaField;
use Armezit\Lunar\VirtualProduct\Data\CodePoolSchemaFieldsList;
use Armezit\Lunar\VirtualProduct\Enums\CodePoolFieldType;
use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class CodePoolSchemaFieldsListTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function can_create_new_instance()
    {
        $fieldsList = new CodePoolSchemaFieldsList(
            fields: CodePoolSchemaField::collection([])
        );

        $this->assertInstanceOf(CodePoolSchemaFieldsList::class, $fieldsList);
    }

    /** @test */
    public function can_access_fields_collection_correctly()
    {
        $fieldsList = new CodePoolSchemaFieldsList(
            fields: CodePoolSchemaField::collection([
                [
                    'name' => $this->faker->word,
                    'type' => CodePoolFieldType::Raw,
                    'order' => 1,
                ],
            ])
        );

        $this->assertCount(1, $fieldsList);
        $this->assertInstanceOf(CodePoolSchemaField::class, $fieldsList[0]);

        // add item
        $fieldsList[] = CodePoolSchemaField::from([
            'name' => $this->faker->word,
            'type' => CodePoolFieldType::Integer,
            'order' => 2,
        ]);

        $this->assertCount(2, $fieldsList);
        $this->assertInstanceOf(CodePoolSchemaField::class, $fieldsList[1]);

        // remove item
        unset($fieldsList[0]);
        $this->assertCount(1, $fieldsList);
        $this->assertEquals(false, isset($fieldsList[0]));
        $this->assertInstanceOf(CodePoolSchemaField::class, $fieldsList[1]);
    }
}
