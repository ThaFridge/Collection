<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('platform', 100)->nullable();
            $table->string('slug', 255);
            $table->string('cover_image_path', 500)->nullable();
            $table->string('cover_image_url', 500)->nullable();
            $table->date('release_date')->nullable();
            $table->string('genre', 255)->nullable();
            $table->string('developer', 255)->nullable();
            $table->string('publisher', 255)->nullable();
            $table->text('description')->nullable();
            $table->decimal('purchase_price', 8, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('condition', 50)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('collection');
            $table->string('completion_status', 30)->default('not_played');
            $table->tinyInteger('rating')->nullable();
            $table->string('format', 20)->default('physical');
            $table->string('barcode', 50)->nullable();
            $table->string('external_api_id', 255)->nullable();
            $table->string('external_api_source', 50)->nullable();
            $table->json('metadata_json')->nullable();
            $table->timestamps();

            $table->unique(['slug', 'platform', 'format']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
