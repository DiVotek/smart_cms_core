<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Page;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(sconfig('database_table_prefix').Page::getDb(), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->integer('sorting')->default(0);
            $table->string('image')->nullable();
            $table->boolean('status')->default(1);
            $table->integer('views')->default(0);
            $table->boolean('is_nav')->default(0);
            $table->timestamps();
        });
    }
};
