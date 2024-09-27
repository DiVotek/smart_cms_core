<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Seo;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('smart_cms.database_table_prefix') . Seo::getDb(), function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('heading')->nullable();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('description')->nullable();
            $table->string('keywords')->nullable();
            $table->morphs('seoable');
            $table->unsignedBigInteger('language_id')->nullable();
            (new Seo)->timestampMigrationFields($table);
        });
    }
};
