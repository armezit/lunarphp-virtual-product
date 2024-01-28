<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Unit\Models;

use Armezit\Lunar\VirtualProduct\Models\CodePoolBatch;
use Armezit\Lunar\VirtualProduct\Models\CodePoolItem;
use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Lunar\Base\Purchasable;
use Lunar\Models\ProductVariant;

class CodePoolItemTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private function getCodePoolItemTable(): string
    {
        return (new CodePoolItem)->getTable();
    }

    /** @test */
    public function can_make_a_code_pool_item()
    {
        $dataFieldValue = [
            'foo' => $this->faker->word,
            'bar' => $this->faker->numberBetween(1, 50),
        ];
        $data = [
            'batch_id' => $this->faker->numberBetween(1, 1000),
            'schema_id' => $this->faker->numberBetween(1, 1000),
        ];
        CodePoolItem::factory()->create(array_merge($data, ['data' => $dataFieldValue]));

        $this->assertDatabaseHas($this->getCodePoolItemTable(), array_merge($data, [
            'data' => $this->castAsJson($dataFieldValue),
        ]));
    }

    /** @test */
    public function code_pool_item_has_correct_casting()
    {
        $codePoolItem = CodePoolItem::factory()->create([
            'data' => [
                'foo' => $this->faker->word,
                'bar' => $this->faker->numberBetween(1, 50),
            ],
        ]);

        $this->assertInstanceOf(ArrayObject::class, $codePoolItem->data);
        $this->assertIsString($codePoolItem->data['foo']);
        $this->assertIsInt($codePoolItem->data['bar']);
    }

    /** @test */
    public function can_associate_to_code_pool_batch()
    {
        $codePoolBatch = CodePoolBatch::factory()->create();

        $codePoolItem = CodePoolItem::factory()->create([
            'batch_id' => $codePoolBatch->id,
        ]);

        $this->assertInstanceOf(CodePoolBatch::class, $codePoolItem->batch);
    }

    /** @test */
    public function can_associate_to_code_pool_schema()
    {
        $codePoolSchema = CodePoolSchema::factory()->create();

        $codePoolItem = CodePoolItem::factory()->create([
            'schema_id' => $codePoolSchema->id,
        ]);

        $this->assertInstanceOf(CodePoolSchema::class, $codePoolItem->schema);
    }

    /** @test */
    public function can_associate_to_purchasable()
    {
        $purchasable = ProductVariant::factory()->create();

        $codePoolBatch = CodePoolBatch::factory()->create([
            'purchasable_type' => $purchasable->getMorphClass(),
            'purchasable_id' => $purchasable->id,
        ]);

        $item = CodePoolItem::factory()->create(['batch_id' => $codePoolBatch->id]);
        $codePoolBatch->refresh();

        $this->assertInstanceOf(Purchasable::class, $item->purchasable);
    }

    /** @test */
    public function query_scope_for_purchasable_is_correct()
    {
        $purchasable = ProductVariant::factory()->create();

        $codePoolBatch = CodePoolBatch::factory()->create([
            'purchasable_type' => $purchasable->getMorphClass(),
            'purchasable_id' => $purchasable->id,
        ]);

        CodePoolItem::factory()
            ->count(10)
            ->sequence(
                ['batch_id' => $codePoolBatch->id],
                ['batch_id' => $this->faker->randomNumber()],
            )
            ->create();

        $items = CodePoolItem::forPurchasable($purchasable->id)->get();

        $this->assertContainsOnly(CodePoolItem::class, $items);
        $this->assertCount(5, $items);
    }
}
