<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Template;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Template::getDb(), function (Blueprint $table) {
            if (! Schema::hasColumn(Template::getDb(), 'status')) {
                $table->boolean('status')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table(Template::getDb(), function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
