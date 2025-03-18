<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Field;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Field::getDb(), function (Blueprint $table) {
            if (! Schema::hasColumn(Field::getDb(), 'data')) {
                $table->json('data')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table(Field::getDb(), function (Blueprint $table) {
            if (Schema::hasColumn(Field::getDb(), 'data')) {
                $table->dropColumn('data');
            }
        });
    }
};
