<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Form;
use SmartCms\Core\Models\Language;
use SmartCms\Core\Models\Translation;

return new class extends Migration
{
   public function up(): void
   {
      Schema::table(Form::getDb(), function (Blueprint $table) {
         $table->json('button')->nullable();
      });
   }

   public function down()
   {
      Schema::table(Form::getDb(), function (Blueprint $table) {
         $table->dropColumn('button');
      });
   }
};
