<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\MenuSection;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(MenuSection::getDb(), function (Blueprint $table) {
            $table->json('categories_template')->nullable();
        });
    }

    public function down()
    {
        Schema::table(MenuSection::getDb(), function (Blueprint $table) {
            $table->dropColumn('categories_template');
        });
    }
};
