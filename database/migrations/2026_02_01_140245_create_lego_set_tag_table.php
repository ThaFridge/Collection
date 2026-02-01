<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lego_set_tag', function (Blueprint $table) {
            $table->foreignId('lego_set_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['lego_set_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lego_set_tag');
    }
};
