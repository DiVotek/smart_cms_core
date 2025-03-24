<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;

class MenuSectionFactory extends Factory
{
    protected $model = MenuSection::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'parent_id' => Page::factory(),
            'sorting' => $this->faker->numberBetween(1, 100),
            'is_categories' => $this->faker->boolean(20),
            'custom_fields' => [],
            'template' => [],
            'categories_template' => [],
        ];
    }
}
