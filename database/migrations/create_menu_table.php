<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Menu;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Menu::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('value')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(Menu::getDb());
    }
};
