<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('magazines', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('publisher')->nullable();
            $table->string('issue_number')->nullable();
            $table->date('publication_date')->nullable();
            $table->unsignedSmallInteger('year');
            $table->string('cover_image_path')->nullable();
            $table->string('pdf_path');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('magazines');
    }
};
