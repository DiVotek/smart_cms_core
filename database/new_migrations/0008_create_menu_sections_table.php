<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(MenuSection::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('sorting');
            $table->string('icon')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on(Page::getDb());
            $table->boolean('is_categories')->default(false);
            $table->unsignedBigInteger('items_layout_id')->nullable();
            $table->foreign('items_layout_id')->references('id')->on(Layout::getDb());
            $table->unsignedBigInteger('categories_layout_id')->nullable();
            $table->foreign('categories_layout_id')->references('id')->on(Layout::getDb());
            $table->json('custom_fields')->nullable();
            $table->json('template')->nullable();
            $table->json('categories_template')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(MenuSection::getDb());
    }
};
