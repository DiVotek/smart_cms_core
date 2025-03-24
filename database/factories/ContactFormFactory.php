<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SmartCms\Core\Models\ContactForm;
use SmartCms\Core\Models\Form;

class ContactFormFactory extends Factory
{
    protected $model = ContactForm::class;

    public function definition(): array
    {
        return [
            'form_id' => Form::factory(),
            'data' => [
                'email' => $this->faker->email(),
                'message' => $this->faker->paragraph(),
                'phone' => $this->faker->phoneNumber(),
            ],
            'comment' => $this->faker->boolean() ? $this->faker->paragraph() : null,
            'status' => $this->faker->randomElement([
                ContactForm::STATUS_NEW,
                ContactForm::STATUS_VIEWED,
                ContactForm::STATUS_CLOSED,
            ]),
        ];
    }
}
