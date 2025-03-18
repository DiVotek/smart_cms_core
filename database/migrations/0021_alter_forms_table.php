<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Form;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Form::getDb(), function (Blueprint $table) {
            if (! Schema::hasColumn(Form::getDb(), 'data')) {
                $table->json('data')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table(Form::getDb(), function (Blueprint $table) {
            if (Schema::hasColumn(Form::getDb(), 'data')) {
                $table->dropColumn('data');
            }
        });
    }
};
