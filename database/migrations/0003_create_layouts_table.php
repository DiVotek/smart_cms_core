<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\Language;
use SmartCms\Core\Models\Layout;

return new class extends Migration
{
   public function up()
   {
      Schema::create(Layout::getDb(), function (Blueprint $table) {
         $table->id();
         $table->string('name');
         $table->string('path');
         $table->json('schema');
         $table->json('value');
         $table->timestamps();
      });
   }

   public function down()
   {
      Schema::dropIfExists(Layout::getDb());
   }
};
