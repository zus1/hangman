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
        Schema::create('tik_blueprints', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('type');
            $table->json('blueprint');
            $table->integer('fields_num');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tik_blueprints');
    }
};
