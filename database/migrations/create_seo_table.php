<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Seo;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Seo::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('heading')->nullable();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('description')->nullable();
            $table->string('keywords')->nullable();
            $table->morphs('seoable');
            $table->unsignedBigInteger('language_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(Seo::getDb());
    }
};
