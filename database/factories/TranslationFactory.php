<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SmartCms\Core\Models\Language;
use SmartCms\Core\Models\Translation;

class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->word().'.'.$this->faker->word(),
            'language_id' => Language::factory(),
            'value' => $this->faker->sentence(),
        ];
    }
}
