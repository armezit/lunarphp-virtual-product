<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Unit\Data;

use Armezit\Lunar\VirtualProduct\Data\CodePoolSchemaField;
use Armezit\Lunar\VirtualProduct\Data\CodePoolSchemaFieldsList;
use Armezit\Lunar\VirtualProduct\Data\ProductSource;
use Armezit\Lunar\VirtualProduct\Data\ProductSourcesList;
use Armezit\Lunar\VirtualProduct\SourceProviders\CodePool;
use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ProductSourcesListTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function can_create_new_instance()
    {
        $sourcesList = new ProductSourcesList(
            sources: ProductSource::collection([])
        );

        $this->assertInstanceOf(ProductSourcesList::class, $sourcesList);
    }

    /** @test */
    public function can_access_sources_collection_correctly()
    {
        $sourcesList = new ProductSourcesList(
            sources: ProductSource::collection([
                [
                    'class' => CodePool::class,
                    'enabled' => $this->faker->boolean,
                ]
            ])
        );

        $this->assertCount(1, $sourcesList);
        $this->assertInstanceOf(ProductSource::class, $sourcesList[0]);

        // add item
        $sourcesList[] = ProductSource::from([
            'class' => CodePool::class,
            'enabled' => $this->faker->boolean,
        ]);

        $this->assertCount(2, $sourcesList);
        $this->assertInstanceOf(ProductSource::class, $sourcesList[1]);

        // remove item
        unset($sourcesList[0]);
        $this->assertCount(1, $sourcesList);
        $this->assertEquals(false, isset($sourcesList[0]));
        $this->assertInstanceOf(ProductSource::class, $sourcesList[1]);
    }
}
