<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Layout;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Layout::getDb(), function (Blueprint $table) {
            if (! Schema::hasColumn(Layout::getDb(), 'status')) {
                $table->boolean('status')->default(true);
            }
            if (! Schema::hasColumn(Layout::getDb(), 'can_be_used')) {
                $table->boolean('can_be_used')->default(false);
            }
            if (! Schema::hasColumn(Layout::getDb(), 'template')) {
                $table->string('template')->default('');
            }
        });
    }

    public function down(): void
    {
        Schema::table(Layout::getDb(), function (Blueprint $table) {
            if (Schema::hasColumn(Layout::getDb(), 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn(Layout::getDb(), 'can_be_used')) {
                $table->dropColumn('can_be_used');
            }
            if (Schema::hasColumn(Layout::getDb(), 'template')) {
                $table->dropColumn('template');
            }
        });
    }
};
