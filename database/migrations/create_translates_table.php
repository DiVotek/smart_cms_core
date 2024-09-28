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
        Schema::create(sconfig('database_table_prefix').Translate::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->foreignIdFor(Language::class)->constrained()->cascadeOnDelete();
            $table->morphs('entity');
            $table->timestamps();
        });
    }
};
