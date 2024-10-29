<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->timestamp('created_at');
            $table->timestamp('expires_at');
            $table->string('token');
            $table->tinyInteger('active')->default(1);
            $table->string('type');
            $table->foreign('user_id')->references('id')->on($this->getUsersTableName())->cascadeOnDelete();
        });
    }

    private function getUsersTableName(): string
    {
        $userClass = config('auth.providers.users.model');
        $parts = explode('\\', $userClass);

        return Str::plural(strtolower(Str::snake($parts[count($parts) - 1])));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
};
