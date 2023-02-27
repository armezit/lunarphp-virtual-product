<?php

namespace Armezit\Lunar\VirtualProduct\Database\Factories;

use Armezit\Lunar\VirtualProduct\Enums\CodePoolFieldType;
use Armezit\Lunar\VirtualProduct\Models\CodePoolBatch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Lunar\Models\ProductVariant;

class CodePoolBatchFactory extends Factory
{
    protected $model = CodePoolBatch::class;

    /**
     * Define the model's default state.
     */
    public function definition()
    {
        return [
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => $this->faker->numberBetween(1, 1000),
            'staff_id' => $this->faker->numberBetween(1, 1000),
            'status' => CodePoolFieldType::Raw->value,
            'entry_price' => $this->faker->randomFloat(4, 1, 5000),
            'entry_price_currency_id' => $this->faker->numberBetween(1, 1000),
            'notes' => $this->faker->realText(100),
        ];
    }
}
