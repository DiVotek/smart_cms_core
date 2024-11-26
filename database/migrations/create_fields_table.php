<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Field;
use SmartCms\Core\Models\Form;

return new class extends Migration
{
   public function up(): void
   {
      Schema::create(Field::getDb(), function (Blueprint $table) {
         $table->id();
         $table->string('name');
         $table->string('type');
         $table->string('html_id');
         $table->string('class')->nullable();
         $table->string('style')->nullable();
         $table->json('label')->nullable();
         $table->json('description')->nullable();
         $table->json('placeholder')->nullable();
         $table->json('options')->nullable();
         $table->boolean('required')->default(false);
         $table->string('validation')->nullable();
         $table->timestamps();
      });
   }

   public function down()
   {
      Schema::dropIfExists(Field::getDb());
   }
};
