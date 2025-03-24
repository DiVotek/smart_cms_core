<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Language;

return new class extends Migration
{
    public function up()
    {
        Schema::create(Language::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('locale');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Create default languages
        foreach (Language::LANGUAGES as $language) {
            Language::query()->create($language);
        }
    }

    public function down()
    {
        Schema::dropIfExists(Language::getDb());
    }
};
