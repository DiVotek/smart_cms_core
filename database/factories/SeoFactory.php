<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SmartCms\Core\Models\Language;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\Seo;

class SeoFactory extends Factory
{
    protected $model = Seo::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'heading' => $this->faker->sentence(),
            'summary' => $this->faker->paragraph(),
            'content' => $this->faker->paragraphs(3, true),
            'description' => $this->faker->paragraph(),
            'keywords' => implode(', ', $this->faker->words(6)),
            'language_id' => Language::factory(),
            'seoable_type' => Page::class,
            'seoable_id' => Page::factory(),
        ];
    }
}
