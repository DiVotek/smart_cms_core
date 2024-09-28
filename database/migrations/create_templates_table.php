<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Template;
use SmartCms\Core\Models\TemplateSection;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(sconfig('database_table_prefix') . Template::getDb(), function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TemplateSection::class);
            $table->morphs('entity');
            $table->integer('sorting')->default(0);
            $table->json('value')->nullable();
            $table->timestamps();
        });
    }
};
