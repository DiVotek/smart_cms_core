<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SmartCms\Core\Models\Field;

class FieldFactory extends Factory
{
    protected $model = Field::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'type' => $this->faker->randomElement(['text', 'select', 'checkbox', 'radio', 'textarea']),
            'html_id' => 'field_' . $this->faker->unique()->word(),
            'data' => [
                'mask' => $this->faker->word(),
                'placeholder' => $this->faker->sentence(3),
                'description' => $this->faker->paragraph(1),
            ],
            'required' => $this->faker->boolean(),
        ];
    }
}
