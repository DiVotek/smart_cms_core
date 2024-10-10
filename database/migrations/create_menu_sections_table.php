<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\MenuSection;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(MenuSection::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('sorting');
            $table->boolean('is_categories')->default(false);
            $table->integer('parent_id')->nullable();
            $table->json('custom_fields')->nullable();
            $table->json('template')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(MenuSection::getDb());
    }
};
