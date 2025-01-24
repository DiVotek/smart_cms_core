<?php

namespace SmartCms\Store\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SmartCms\Core\Models\Page;

class PageFactory extends Factory
{
   protected $model = Page::class;

   public function definition()
   {
      return [
         'name' => $this->faker->word,
         'slug' => $this->faker->slug,
         'sorting' => $this->faker->numberBetween(1, 100),
         'status' => $this->faker->boolean,
         'image' => $this->faker->imageUrl(),
         'banner' => $this->faker->imageUrl(),
         'parent_id' => null,
         'layout_id' => null,
         'custom' => []
      ];
   }
}
