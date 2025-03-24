<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Page;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Page::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->integer('sorting')->default(0);
            $table->string('image')->nullable();
            $table->string('banner')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->unsignedBigInteger('layout_id')->nullable()->index();
            $table->boolean('status')->default(true);
            $table->integer('views')->default(0);
            $table->json('custom')->nullable();
            $table->json('layout_settings')->nullable();
            $table->timestamps();
        });
        Page::query()->create([
            'name' => 'Home',
            'slug' => '',
            'status' => 1,
        ]);
    }

    public function down()
    {
        Schema::dropIfExists(Page::getDb());
    }
};
