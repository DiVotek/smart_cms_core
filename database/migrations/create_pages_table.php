<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Page;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(config('smart_cms.database_table_prefix') . Page::getDb(), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('sorting')->default(0);
            $table->string('image')->nullable();
            $table->boolean('status')->default(1);
            $table->integer('views')->default(0);
            $table->timestamps();
        });
    }
};
