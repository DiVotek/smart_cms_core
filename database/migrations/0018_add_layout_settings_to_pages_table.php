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
            $table->json('layout_settings')->nullable()->after('layout_id');
        });
    }

    public function down(): void
    {
        Schema::table(Page::getDb(), function (Blueprint $table) {
            $table->dropColumn('layout_settings');
        });
    }
};
