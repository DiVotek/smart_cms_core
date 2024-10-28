<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Email;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(Email::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('users');
            $table->string('subject');
            $table->text('content');
            $table->boolean('is_sent')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Email::getDb());
    }
};
