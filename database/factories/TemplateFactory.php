<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\Template;
use SmartCms\Core\Models\TemplateSection;

class TemplateFactory extends Factory
{
    protected $model = Template::class;

    public function definition(): array
    {
        return [
            'template_section_id' => TemplateSection::factory(),
            'sorting' => $this->faker->numberBetween(1, 100),
            'status' => $this->faker->boolean(80),
            'value' => null,
            'entity_type' => Page::class,
            'entity_id' => Page::factory(),
        ];
    }
}
