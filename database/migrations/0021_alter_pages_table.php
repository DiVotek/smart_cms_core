<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Page;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Page::getDb(), function (Blueprint $table) {
            if (! Schema::hasColumn(Page::getDb(), 'layout_id')) {
                $table->foreignId('layout_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table(Page::getDb(), function (Blueprint $table) {
            if (Schema::hasColumn(Page::getDb(), 'layout_id')) {
                $table->dropColumn('layout_id');
            }
        });
    }
};
