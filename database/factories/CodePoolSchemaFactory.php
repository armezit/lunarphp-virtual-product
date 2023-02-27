<?php

namespace Armezit\Lunar\VirtualProduct\Database\Factories;

use Armezit\Lunar\VirtualProduct\Enums\CodePoolFieldType;
use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Illuminate\Database\Eloquent\Factories\Factory;

class CodePoolSchemaFactory extends Factory
{
    protected $model = CodePoolSchema::class;

    /**
     * Define the model's default state.
     */
    public function definition()
    {
        $fields = [];
        for ($i = 0; $i < $this->faker->numberBetween(1, 5); $i++) {
            $fields[] = [
                'name' => $this->faker->word,
                'type' => CodePoolFieldType::Raw->value,
                'order' => $i,
            ];
        }

        return [
            'name' => $this->faker->word,
            'fields' => $fields,
        ];
    }
}
