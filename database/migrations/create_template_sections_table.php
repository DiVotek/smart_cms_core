<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\TemplateSection;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(TemplateSection::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('status')->default(true);
            $table->boolean('locked')->default(false);
            $table->boolean('is_system')->default(false);
            $table->string('design')->nullable();
            $table->json('value')->nullable();
            $table->json('schema')->nullable();
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists(TemplateSection::getDb());
    }
};
