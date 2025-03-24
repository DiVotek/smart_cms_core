<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Field;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Field::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('html_id');
            // Removed many columns that were in old migration but not in model
            // - mask, class, style, label, description, placeholder, options, validation
            $table->boolean('required')->default(false);
            // Added from later migration 0022
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(Field::getDb());
    }
};
