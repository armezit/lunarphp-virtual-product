<?php

namespace Armezit\Lunar\VirtualProduct\Database\Factories;

use Armezit\Lunar\VirtualProduct\Models\CodePoolArchive;
use Illuminate\Database\Eloquent\Factories\Factory;

class CodePoolArchiveFactory extends Factory
{
    protected $model = CodePoolArchive::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'batch_id' => $this->faker->numberBetween(1, 1000),
            'schema_id' => $this->faker->numberBetween(1, 1000),
            'order_line_id' => $this->faker->numberBetween(1, 1000),
            'data' => [],
        ];
    }
}
