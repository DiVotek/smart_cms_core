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
            $table->boolean('status')->default(true);
            $table->string('code')->unique();
            $table->string('name');
            $table->string('html_id')->nullable();
            $table->string('class')->nullable();
            $table->string('style');
            $table->json('fields');
            $table->json('notification')->nullable();
            $table->boolean('is_send_email')->default(false);
            $table->json('email_text')->nullable();
            $table->string('email_template')->nullable();
            $table->json('button')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(Form::getDb());
    }
};
