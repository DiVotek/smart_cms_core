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
            $table->references('id')->on(Language::getDb());
            $table->foreignIdFor(Language::class)->constrained()->cascadeOnDelete()->references('id')->on('smart_cms_languages');
            $table->morphs('entity');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(Translate::getDb());
    }
};
