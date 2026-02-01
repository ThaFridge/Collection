<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_platforms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->string('platform', 50);
            $table->string('format', 20)->default('physical');
            $table->string('status', 20)->default('collection');
            $table->string('completion_status', 20)->default('not_played');
            $table->decimal('purchase_price', 8, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('condition', 50)->nullable();
            $table->string('barcode', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['game_id', 'platform', 'format']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_platforms');
    }
};
