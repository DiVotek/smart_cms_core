<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SmartCms\Core\Models\Language;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\Translate;

class TranslateFactory extends Factory
{
    protected $model = Translate::class;

    public function definition(): array
    {
        return [
            'value' => $this->faker->paragraph(),
            'language_id' => Language::factory(),
            'translatable_type' => Page::class,
            'translatable_id' => Page::factory(),
        ];
    }
}
