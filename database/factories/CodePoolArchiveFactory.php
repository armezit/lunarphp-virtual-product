<?php

namespace Armezit\GetCandy\VirtualProduct\Database\Factories;

use Armezit\GetCandy\VirtualProduct\Models\CodePoolArchive;
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
        $data = [
            'name' => $this->faker->word(),
            'value' => $this->faker->numberBetween(1000),
        ];

        return [
            'batch_id' => 1,
            'order_line_id' => 1,
            'data' => $data,
        ];
    }
}
