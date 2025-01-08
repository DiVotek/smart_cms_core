<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Language;
use SmartCms\Core\Models\Translate;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Translate::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->unsignedBigInteger('language_id');
            $table->foreign('language_id')->references('id')->on(Language::getDb());
            $table->morphs('entity');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(Translate::getDb());
    }
};
