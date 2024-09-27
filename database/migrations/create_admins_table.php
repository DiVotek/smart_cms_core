<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Admin;

return new class extends Migration
{
    public function up()
    {
        Schema::create(config('smart_cms.database_table_prefix') . Admin::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email')->unique()->index();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }
};
