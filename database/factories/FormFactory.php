<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SmartCms\Core\Models\Form;

class FormFactory extends Factory
{
    protected $model = Form::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'status' => $this->faker->boolean(80),
            'code' => $this->faker->unique()->word(),
            'html_id' => 'form_'.$this->faker->word(),
            'class' => 'form '.$this->faker->word(),
            'fields' => [],
            'button' => null,
            'notification' => null,
            'data' => [
                'button' => 'Submit',
                'notification' => 'Form submitted successfully',
            ],
        ];
    }
}
