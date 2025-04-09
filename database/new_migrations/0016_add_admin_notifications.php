<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Admin;

return new class extends Migration
{
    public function up()
    {
        Schema::table(sconfig('database_table_prefix') . Admin::getDb(), function (Blueprint $table) {
            $table->string('telegram_id')->nullable();
            $table->json('notifications')->nullable();
        });
    }

    public function down()
    {
        Schema::table(sconfig('database_table_prefix') . Admin::getDb(), function (Blueprint $table) {
            $table->dropColumn('telegram_id');
            $table->dropColumn('telegram_token');
            $table->dropColumn('notifications');
        });
    }
};
