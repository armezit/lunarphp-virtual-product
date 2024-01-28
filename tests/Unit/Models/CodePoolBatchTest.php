<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Unit\Models;

use Armezit\Lunar\VirtualProduct\Models\CodePoolBatch;
use Armezit\Lunar\VirtualProduct\Models\CodePoolItem;
use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Lunar\Base\Purchasable;
use Lunar\Models\Currency;
use Lunar\Models\ProductVariant;

class CodePoolBatchTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private function getCodePoolBatchTable(): string
    {
        return (new CodePoolBatch)->getTable();
    }

    /** @test */
    public function can_make_a_code_pool_batch_with_minimum_attributes()
    {
        $data = [
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => 1,
        ];
        CodePoolBatch::factory()->create($data);

        $this->assertDatabaseHas($this->getCodePoolBatchTable(), $data);
    }

    /** @test */
    public function can_make_a_code_pool_batch()
    {
        $data = [
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => $this->faker->numberBetween(1, 1000),
            'entry_price' => $this->faker->randomFloat(4, 1, 5000),
            'entry_price_currency_id' => $this->faker->numberBetween(1, 1000),
            'notes' => $this->faker->realText(100),
        ];

        CodePoolBatch::factory()->create($data);

        $this->assertDatabaseHas($this->getCodePoolBatchTable(), $data);
    }

    /** @test */
    public function code_pool_batch_has_correct_casting()
    {
        $currency = Currency::factory()->create();

        $codePoolBatch = CodePoolBatch::factory()->create([
            'entry_price_currency_id' => $currency->id,
        ]);

        $this->assertIsFloat($codePoolBatch->entry_price);
    }

    /** @test */
    public function can_associate_to_purchasable()
    {
        $purchasable = ProductVariant::factory()->create();

        $codePoolBatch = CodePoolBatch::factory()->create([
            'purchasable_type' => $purchasable->getMorphClass(),
            'purchasable_id' => $purchasable->id,
        ]);

        $this->assertInstanceOf(Purchasable::class, $codePoolBatch->purchasable);
    }

    /** @test */
    public function can_associate_to_currency()
    {
        $currency = Currency::factory()->create();

        $codePoolBatch = CodePoolBatch::factory()->create([
            'entry_price' => $this->faker->numberBetween(1, 100),
            'entry_price_currency_id' => $currency->id,
        ]);

        $this->assertInstanceOf(Currency::class, $codePoolBatch->entryPriceCurrency);
    }

    /** @test */
    public function can_associate_to_code_pool_items()
    {
        $purchasable = ProductVariant::factory()->create();

        $codePoolBatch = CodePoolBatch::factory()->create([
            'purchasable_type' => $purchasable->getMorphClass(),
            'purchasable_id' => $purchasable->id,
        ]);

        CodePoolItem::factory(3)->create(['batch_id' => $codePoolBatch->id]);
        $codePoolBatch->refresh();

        $this->assertContainsOnly(CodePoolItem::class, $codePoolBatch->items);
        $this->assertCount(3, $codePoolBatch->items);
    }
}
