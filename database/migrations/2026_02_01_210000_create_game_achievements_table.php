<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->decimal('percent', 5, 2)->nullable(); // global unlock percentage
            $table->timestamps();
        });

        // Track whether achievements were fetched (and if game supports them)
        Schema::table('games', function (Blueprint $table) {
            $table->boolean('achievements_fetched')->default(false);
            $table->boolean('achievements_supported')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_achievements');

        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['achievements_fetched', 'achievements_supported']);
        });
    }
};
