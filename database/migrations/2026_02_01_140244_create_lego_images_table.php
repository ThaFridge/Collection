<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lego_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lego_set_id')->constrained()->cascadeOnDelete();
            $table->string('image_path', 500);
            $table->string('type', 30)->default('photo');
            $table->integer('sort_order')->default(0);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lego_images');
    }
};
