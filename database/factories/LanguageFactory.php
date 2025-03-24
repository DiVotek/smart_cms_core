<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SmartCms\Core\Models\Language;

class LanguageFactory extends Factory
{
    protected $model = Language::class;

    public function definition(): array
    {
        $languages = array_keys(Language::LANGUAGES);
        $language = $this->faker->randomElement($languages);
        $languageData = Language::LANGUAGES[$language];

        return [
            'name' => $languageData['name'],
            'slug' => $languageData['slug'],
            'locale' => $languageData['locale'],
            'status' => $languageData['status'],
        ];
    }
}
