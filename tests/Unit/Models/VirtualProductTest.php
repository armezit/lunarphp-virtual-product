<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Unit\Models;

use Armezit\Lunar\VirtualProduct\Models\VirtualProduct;
use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Lunar\Models\Product;

class VirtualProductTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private function getVirtualProductTable(): string
    {
        return (new VirtualProduct)->getTable();
    }

    /** @test */
    public function can_make_a_virtual_product_with_minimum_attributes()
    {
        $data = [
            'product_id' => $this->faker->numberBetween(1, 1000),
            'source' => $this->faker->name,
        ];
        VirtualProduct::create($data);

        $this->assertDatabaseHas($this->getVirtualProductTable(), $data);
    }

    /** @test */
    public function can_make_a_virtual_product()
    {
        $data = [
            'product_id' => $this->faker->numberBetween(1, 1000),
            'source' => $this->faker->name,
        ];
        $meta = [
            'foo' => 'value',
            'bar' => 20,
        ];
        VirtualProduct::factory()->create(array_merge($data, ['meta' => $meta]));

        $this->assertDatabaseHas($this->getVirtualProductTable(), array_merge($data, [
            'meta' => $this->castAsJson($meta),
        ]));

        $this->assertDatabaseHas($this->getVirtualProductTable(), [
            'meta->foo' => 'value',
            'meta->bar' => 20,
        ]);
    }

    /** @test */
    public function virtual_product_has_correct_casting()
    {
        /** @var VirtualProduct $virtualProduct */
        $virtualProduct = VirtualProduct::factory()->create([
            'meta' => [
                'foo' => $this->faker->word,
                'bar' => $this->faker->numberBetween(1, 50),
            ],
        ]);

        $this->assertInstanceOf(ArrayObject::class, $virtualProduct->meta);
        $this->assertIsString($virtualProduct->meta['foo']);
        $this->assertIsInt($virtualProduct->meta['bar']);
    }

    /** @test */
    public function can_associate_to_product()
    {
        $product = Product::factory()->create();
        $virtualProduct = VirtualProduct::factory()->create([
            'product_id' => $product->id,
        ]);

        $this->assertInstanceOf(Product::class, $virtualProduct->product);
    }
}
