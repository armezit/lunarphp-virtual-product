<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Unit\Models;

use Armezit\Lunar\VirtualProduct\Models\VirtualProduct;
use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Lunar\Models\Customer;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;

class VirtualProductTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_can_make_a_virtual_product_with_minimum_attributes()
    {
        $limit = [];
        VirtualProduct::create($limit);

        $this->assertDatabaseHas('virtual_products', $limit);
    }

    public function test_can_make_a_virtual_product()
    {
        $limit = [
            'product_variant_id' => $this->faker->numberBetween(1, 1000),
            'product_id' => $this->faker->numberBetween(1, 1000),
            'customer_group_id' => $this->faker->numberBetween(1, 1000),
            'customer_id' => $this->faker->numberBetween(1, 1000),
            'period' => $this->faker->numberBetween(1, 10),
            'max_quantity' => $this->faker->numberBetween(1, 10),
            'max_total' => $this->faker->numberBetween(1, 1000),
            'starts_at' => $this->faker->date(),
            'ends_at' => $this->faker->date(),
        ];
        VirtualProduct::create($limit);

        $this->assertDatabaseHas('virtual_products', $limit);
    }

    public function test_can_associate_to_product()
    {
        $product = Product::factory()->create();
        $virtualProduct = VirtualProduct::factory()->create([
            'product_id' => $product->id,
        ]);

        $this->assertInstanceOf(Product::class, $virtualProduct->product);
    }

    public function test_can_associate_to_product_variant()
    {
        $productVariant = ProductVariant::factory()->create();
        $virtualProduct = VirtualProduct::factory()->create([
            'product_variant_id' => $productVariant->id,
        ]);

        $this->assertInstanceOf(ProductVariant::class, $virtualProduct->productVariant);
    }

    public function test_can_associate_to_customer()
    {
        $customer = Customer::factory()->create();
        $virtualProduct = VirtualProduct::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $this->assertInstanceOf(Customer::class, $virtualProduct->customer);
    }

    public function test_can_associate_to_customer_group()
    {
        $customerGroup = CustomerGroup::factory()->create();
        $virtualProduct = VirtualProduct::factory()->create([
            'customer_group_id' => $customerGroup->id,
        ]);

        $this->assertInstanceOf(CustomerGroup::class, $virtualProduct->customerGroup);
    }
}
