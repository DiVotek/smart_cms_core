<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Field;
use SmartCms\Core\Models\MenuSection;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Field::getDb(), function (Blueprint $table) {
            $table->json('mask')->nullable();
        });
    }

    public function down()
    {
        Schema::table(MenuSection::getDb(), function (Blueprint $table) {
            $table->dropColumn('mask');
        });
    }
};
