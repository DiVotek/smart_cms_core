<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\TemplateSection;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('smart_cms.database_table_prefix') . TemplateSection::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('status')->default(true);
            $table->boolean('locked')->default(false);
            $table->boolean('is_system')->default(false);
            $table->string('design')->nullable();
            $table->json('value')->nullable();
            $table->timestamps();
        });

    }
};
