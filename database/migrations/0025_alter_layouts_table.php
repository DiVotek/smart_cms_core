<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Models\Template;
use SmartCms\Core\Models\TemplateSection;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(TemplateSection::getDb(), function (Blueprint $table) {
            $table->dropColumn('template');
            $table->dropColumn('locked');
            $table->dropColumn('is_system');
            $table->dropColumn('type');
        });
        Schema::table(Layout::getDb(), function (Blueprint $table) {
            $table->dropColumn('template');
        });
    }

    public function down(): void
    {
        Schema::table(Template::getDb(), function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
