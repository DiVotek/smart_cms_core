<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SmartCms\Core\Models\Layout;

class LayoutFactory extends Factory
{
    protected $model = Layout::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'path' => $this->faker->word(),
            'can_be_used' => $this->faker->boolean(80),
            'status' => $this->faker->boolean(80),
            'schema' =>  [
                [
                    'name' => 'text',
                    'type' => 'text',
                ],
            ],
            'value' => [],
        ];
    }
}
