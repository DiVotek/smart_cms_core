<?php

namespace SmartCms\Store\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Models\Page;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->slug(),
            'sorting' => $this->faker->numberBetween(1, 100),
            'image' => 'storage/pages/'.$this->faker->image('public/storage/pages', 800, 600, null, false),
            'status' => $this->faker->boolean(80),
            'parent_id' => $this->faker->boolean(20) ? Page::factory() : null,
            'views' => $this->faker->numberBetween(0, 10000),
            'layout_id' => Layout::factory(),
            'custom' => null,
            'layout_settings' => null,
        ];
    }
}
