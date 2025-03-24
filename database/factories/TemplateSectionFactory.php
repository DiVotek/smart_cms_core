<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SmartCms\Core\Models\TemplateSection;

class TemplateSectionFactory extends Factory
{
    protected $model = TemplateSection::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'design' => $this->faker->word(),
            'status' => $this->faker->boolean(80),
            'schema' => [
                [
                    'name' => 'title',
                    'type' => 'text',
                    'label' => 'Title',
                ],
                [
                    'name' => 'content',
                    'type' => 'textarea',
                    'label' => 'Content',
                ],
                [
                    'name' => 'button_text',
                    'type' => 'text',
                    'label' => 'Button Text',
                ],
                [
                    'name' => 'button_url',
                    'type' => 'text',
                    'label' => 'Button URL',
                ],
            ],
            'value' => [],
        ];
    }
}
