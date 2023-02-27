<?php

namespace Armezit\Lunar\VirtualProduct\Database\Factories;

use Armezit\Lunar\VirtualProduct\Models\VirtualProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class VirtualProductFactory extends Factory
{
    protected $model = VirtualProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'product_id' => $this->faker->numberBetween(1, 1000),
            'source' => $this->faker->name,
        ];
    }
}
