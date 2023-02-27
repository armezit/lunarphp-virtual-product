<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Unit\Models;

use Armezit\Lunar\VirtualProduct\Models\CodePoolArchive;
use Armezit\Lunar\VirtualProduct\Models\CodePoolBatch;
use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Lunar\Models\OrderLine;
use Lunar\Models\ProductVariant;

class CodePoolArchiveTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private function getCodePoolArchiveTable(): string
    {
        return (new CodePoolArchive)->getTable();
    }

    /** @test */
    public function can_make_a_code_pool_archive()
    {
        $dataFieldValue = [
            'foo' => $this->faker->word,
            'bar' => $this->faker->numberBetween(1, 50),
        ];
        $data = [
            'batch_id' => $this->faker->numberBetween(1, 1000),
            'schema_id' => $this->faker->numberBetween(1, 1000),
            'order_line_id' => $this->faker->numberBetween(1, 1000),
        ];
        CodePoolArchive::factory()->create(array_merge($data, ['data' => $dataFieldValue]));

        $this->assertDatabaseHas($this->getCodePoolArchiveTable(), array_merge($data, [
            'data' => $this->castAsJson($dataFieldValue),
        ]));
    }

    /** @test */
    public function code_pool_archive_has_correct_casting()
    {
        $codePoolArchive = CodePoolArchive::factory()->create([
            'data' => [
                'foo' => $this->faker->word,
                'bar' => $this->faker->numberBetween(1, 50),
            ],
        ]);

        $this->assertInstanceOf(ArrayObject::class, $codePoolArchive->data);
        $this->assertIsString($codePoolArchive->data['foo']);
        $this->assertIsInt($codePoolArchive->data['bar']);
    }

    /** @test */
    public function can_associate_to_code_pool_batch()
    {
        $codePoolBatch = CodePoolBatch::factory()->create();

        $codePoolArchive = CodePoolArchive::factory()->create([
            'batch_id' => $codePoolBatch->id,
        ]);

        $this->assertInstanceOf(CodePoolBatch::class, $codePoolArchive->batch);
    }

    /** @test */
    public function can_associate_to_code_pool_schema()
    {
        $codePoolSchema = CodePoolSchema::factory()->create();

        $codePoolArchive = CodePoolArchive::factory()->create([
            'schema_id' => $codePoolSchema->id,
        ]);

        $this->assertInstanceOf(CodePoolSchema::class, $codePoolArchive->schema);
    }

    /** @test */
    public function can_associate_to_order_line()
    {
        $orderLine = OrderLine::factory()->create([
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => ProductVariant::factory()->create()->id,
        ]);

        $codePoolArchive = CodePoolArchive::factory()->create([
            'order_line_id' => $orderLine->id,
        ]);

        $this->assertInstanceOf(OrderLine::class, $codePoolArchive->orderLine);
    }
}
