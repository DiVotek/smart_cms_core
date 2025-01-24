<?php

namespace SmartCms\Store\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SmartCms\Core\Models\Layout;

class LayoutFactory extends Factory
{
    protected $model = Layout::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'path' => $this->faker->word,
            'schema' => [],
            'value' => [],
            'status' => $this->faker->boolean,
            'template' => template(),
        ];
    }
}
