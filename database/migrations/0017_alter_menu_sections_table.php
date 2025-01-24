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
            $table->unsignedBigInteger('items_layout_id')->nullable();
            $table->unsignedBigInteger('categories_layout_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table(MenuSection::getDb(), function (Blueprint $table) {
            $table->dropColumn('items_layout_id');
            $table->dropColumn('categories_layout_id');
        });
    }
};
