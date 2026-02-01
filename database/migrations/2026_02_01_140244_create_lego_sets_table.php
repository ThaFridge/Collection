<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lego_sets', function (Blueprint $table) {
            $table->id();
            $table->string('set_number', 20)->unique();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->string('theme', 100)->nullable();
            $table->string('subtheme', 100)->nullable();
            $table->integer('piece_count')->nullable();
            $table->integer('minifigure_count')->nullable();
            $table->string('image_path', 500)->nullable();
            $table->string('image_url', 500)->nullable();
            $table->integer('release_year')->nullable();
            $table->decimal('retail_price', 8, 2)->nullable();
            $table->decimal('purchase_price', 8, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('condition', 50)->nullable();
            $table->string('status', 20)->default('collection');
            $table->string('build_status', 30)->default('not_built');
            $table->text('notes')->nullable();
            $table->string('instructions_url', 500)->nullable();
            $table->string('bricklink_url', 500)->nullable();
            $table->string('external_api_id', 255)->nullable();
            $table->string('external_api_source', 50)->nullable();
            $table->json('metadata_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lego_sets');
    }
};
