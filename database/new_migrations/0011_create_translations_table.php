<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Language;
use SmartCms\Core\Models\Translation;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Translation::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->foreignIdFor(Language::class);
            $table->string('value')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(Translation::getDb());
    }
};
