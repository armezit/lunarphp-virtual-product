<?php

namespace Armezit\Lunar\VirtualProduct\Database\Factories;

use Armezit\Lunar\VirtualProduct\Models\CodePoolItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class CodePoolItemFactory extends Factory
{
    protected $model = CodePoolItem::class;

    /**
     * Define the model's default state.
     */
    public function definition()
    {
        return [
            'batch_id' => $this->faker->numberBetween(1, 1000),
            'schema_id' => $this->faker->numberBetween(1, 1000),
            'data' => [],
        ];
    }
}
