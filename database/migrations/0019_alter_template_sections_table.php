<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\TemplateSection;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(TemplateSection::getDb(), function (Blueprint $table) {
            if (!Schema::hasColumn(TemplateSection::getDb(), 'template')) {
                $table->string('template')->default('');
            }
        });
    }

    public function down(): void
    {
        Schema::table(TemplateSection::getDb(), function (Blueprint $table) {
            $table->dropColumn('template');
        });
    }
};
