<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SmartCms\Core\Models\ContactForm;
use SmartCms\Core\Models\Form;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('smart_cms.database_table_prefix').ContactForm::getDb(), function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Form::class)->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyInteger('status')->default(ContactForm::STATUS_NEW);
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }
};
