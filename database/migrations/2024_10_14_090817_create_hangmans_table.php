<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hangmans', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->json('word_spaces')->nullable();
            $table->string('image');
            $table->json('guesses')->nullable();
            $table->integer('mistakes')->default(0);
            $table->integer('max_mistakes');
            $table->string('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hangmans');
    }
};
