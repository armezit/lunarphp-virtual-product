<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Unit\Actions\CodePool;

use Armezit\Lunar\VirtualProduct\Actions\CodePool\PullOrderLineItem;
use Armezit\Lunar\VirtualProduct\Enums\CodePoolFieldType;
use Armezit\Lunar\VirtualProduct\Exceptions\CodePool\OutOfStockException;
use Armezit\Lunar\VirtualProduct\Models\CodePoolArchive;
use Armezit\Lunar\VirtualProduct\Models\CodePoolBatch;
use Armezit\Lunar\VirtualProduct\Models\CodePoolItem;
use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Lunar\Models\OrderLine;
use Lunar\Models\ProductVariant;

class PullOrderLineItemTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private function getCodePoolItemTable(): string
    {
        return (new CodePoolItem())->getTable();
    }

    private function getCodePoolArchiveTable(): string
    {
        return (new CodePoolArchive())->getTable();
    }

    /** @test */
    public function can_pull_code_pool_item_correctly()
    {
        $purchasable = ProductVariant::factory()->create();

        $codePoolBatch = CodePoolBatch::factory()->create([
            'purchasable_type' => $purchasable->getMorphClass(),
            'purchasable_id' => $purchasable->id,
        ]);

        $codePoolSchema = CodePoolSchema::factory()->create([
            'name' => $this->faker->word,
            'fields' => [
                ['name' => $this->faker->word, 'type' => CodePoolFieldType::Raw, 'order' => 1],
                ['name' => $this->faker->word, 'type' => CodePoolFieldType::Integer, 'order' => 2],
            ],
        ]);

        CodePoolItem::factory()
            ->count(5)
            ->sequence(
                ['batch_id' => $codePoolBatch->id],
                ['batch_id' => $this->faker->randomNumber()],
            )
            ->state(new Sequence(
                fn (Sequence $sequence) => [
                    'data' => [
                        'foo' => $this->faker->word,
                        'bar' => $this->faker->numberBetween(1, 1000),
                    ]],
            ))
            ->create([
                'schema_id' => $codePoolSchema->id,
            ]);

        $orderLine = OrderLine::factory()->create([
            'purchasable_type' => $purchasable->getMorphClass(),
            'purchasable_id' => $purchasable->id,
            'quantity' => 3,
        ]);

        app(PullOrderLineItem::class)->execute($orderLine);

        $this->assertDatabaseCount($this->getCodePoolArchiveTable(), 3);
        $this->assertDatabaseHas($this->getCodePoolArchiveTable(), [
            'batch_id' => $codePoolBatch->id,
            'schema_id' => $codePoolSchema->id,
            'order_line_id' => $orderLine->id,
        ]);

        $this->assertDatabaseCount($this->getCodePoolItemTable(), 2);
    }

    /** @test */
    public function throws_exception_if_purchasable_is_out_of_stock()
    {
        $purchasable = ProductVariant::factory()->create();

        $codePoolBatch = CodePoolBatch::factory()->create([
            'purchasable_type' => $purchasable->getMorphClass(),
            'purchasable_id' => $purchasable->id,
        ]);

        $codePoolSchema = CodePoolSchema::factory()->create([
            'name' => $this->faker->word,
            'fields' => [
                ['name' => $this->faker->word, 'type' => CodePoolFieldType::Raw, 'order' => 1],
                ['name' => $this->faker->word, 'type' => CodePoolFieldType::Integer, 'order' => 2],
            ],
        ]);

        CodePoolItem::factory()
            ->count(2)
            ->state(new Sequence(
                fn (Sequence $sequence) => [
                    'data' => [
                        'foo' => $this->faker->word,
                        'bar' => $this->faker->numberBetween(1, 1000),
                    ]],
            ))
            ->create([
                'batch_id' => $codePoolBatch->id,
                'schema_id' => $codePoolSchema->id,
            ]);

        $orderLine = OrderLine::factory()->create([
            'purchasable_type' => $purchasable->getMorphClass(),
            'purchasable_id' => $purchasable->id,
            'quantity' => 3,
        ]);

        $this->expectException(OutOfStockException::class);

        app(PullOrderLineItem::class)->execute($orderLine);
    }
}
