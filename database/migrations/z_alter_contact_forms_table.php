<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\ContactForm;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(ContactForm::getDb(), function (Blueprint $table) {
            $table->string('comment')->nullable();
        });
    }

    public function down()
    {
        Schema::table(ContactForm::getDb(), function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }
};
