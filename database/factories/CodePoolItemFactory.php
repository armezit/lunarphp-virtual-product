<?php

namespace Armezit\GetCandy\VirtualProduct\Database\Factories;

use Armezit\GetCandy\VirtualProduct\Models\CodePoolItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CodePoolItemFactory extends Factory
{
    protected $model = CodePoolItem::class;

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
            'data' => $data,
        ];
    }
}
