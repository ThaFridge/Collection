<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing game data to game_platforms
        $games = DB::table('games')->get();
        foreach ($games as $game) {
            DB::table('game_platforms')->insert([
                'game_id' => $game->id,
                'platform' => $game->platform ?? 'Unknown',
                'format' => $game->format ?? 'physical',
                'status' => $game->status ?? 'collection',
                'completion_status' => $game->completion_status ?? 'not_played',
                'purchase_price' => $game->purchase_price,
                'purchase_date' => $game->purchase_date,
                'condition' => $game->condition,
                'barcode' => $game->barcode,
                'created_at' => $game->created_at,
                'updated_at' => $game->updated_at,
            ]);
        }

        // Drop unique index that references platform column (SQLite requirement)
        Schema::table('games', function (Blueprint $table) {
            $table->dropUnique('games_slug_platform_format_unique');
        });

        // Remove migrated columns from games table
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn([
                'platform', 'format', 'status', 'completion_status',
                'purchase_price', 'purchase_date', 'condition', 'barcode',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('platform', 50)->nullable()->after('name');
            $table->string('format', 20)->default('physical')->after('slug');
            $table->string('status', 20)->default('collection');
            $table->string('completion_status', 20)->default('not_played');
            $table->decimal('purchase_price', 8, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('condition', 50)->nullable();
            $table->string('barcode', 50)->nullable();
        });
    }
};
