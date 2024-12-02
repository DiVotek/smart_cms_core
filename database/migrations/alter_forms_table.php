<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Form;
use SmartCms\Core\Models\MenuSection;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Form::getDb(), function (Blueprint $table) {
            $table->json('notification')->nullable();
            $table->boolean('is_send_email')->default(false);
            $table->json('email_text')->nullable();
            $table->string('email_template')->nullable();
        });
    }

    public function down()
    {
        Schema::table(MenuSection::getDb(), function (Blueprint $table) {
            $table->dropColumn('notification');
            $table->dropColumn('is_send_email')->default(false);
            $table->dropColumn('email_text')->nullable();
            $table->dropColumn('email_template')->nullable();
        });
    }
};
