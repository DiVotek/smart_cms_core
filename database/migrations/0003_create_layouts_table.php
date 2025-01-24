<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Layout;

return new class extends Migration
{
    public function up()
    {
        Schema::create(Layout::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->boolean('status')->default(true);
            $table->boolean('can_be_used')->default(false);
            $table->string('template');
            $table->json('schema');
            $table->json('value');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(Layout::getDb());
    }
};
