<?php

namespace Armezit\Lunar\VirtualProduct\Database\Factories;

use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CodePoolSchemaFactory extends Factory
{
    protected $model = CodePoolSchema::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $schema = [];
        for ($i = 0; $i < $this->faker->numberBetween(1, 5); $i++) {
            $name = $this->faker->word();
            $schema[] = [
                'name' => $name,
                'label' => Str::camel($name),
            ];
        }

        return [
            'product_id' => 1,
            'schema' => $schema,
        ];
    }
}
