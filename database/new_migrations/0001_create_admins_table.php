<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Admin;

return new class extends Migration
{
    public function up()
    {
        Schema::create(sconfig('database_table_prefix') . Admin::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email')->unique()->index();
            $table->string('password');
            $table->string('telegram_id')->nullable();
            $table->json('notifications')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Create default admin user
        Admin::query()->create([
            'username' => 'superadmin',
            'email' => 'admin@admin.com',
            'password' => env('ADMIN_PASSWORD', 'password'),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists(sconfig('database_table_prefix') . Admin::getDb());
    }
};
