<?php

namespace Armezit\Lunar\VirtualProduct\Database\Factories;

use Armezit\Lunar\VirtualProduct\Models\CodePoolBatch;
use Lunar\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class CodePoolBatchFactory extends Factory
{
    protected $model = CodePoolBatch::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => 1,
            'entry_price' => $this->faker->numberBetween(1, 2500),
            'notes' => $this->faker->realText(100),
        ];
    }
}
