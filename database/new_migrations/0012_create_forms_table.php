<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Form;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Form::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('status')->default(true);
            $table->string('code')->unique();
            $table->string('html_id')->nullable();
            $table->string('class')->nullable();
            // Removed style field which was in old migration
            $table->json('fields')->nullable();
            $table->json('button')->nullable();
            $table->json('notification')->nullable();
            // Removed is_send_email field which was in old migration
            // Removed email_text field which was in old migration
            // Removed email_template field which was in old migration
            // Added from later migration 0021
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(Form::getDb());
    }
};
