<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "INSERT INTO messages (language_id, key, message) VALUES ".
            "(1, 'new', 'New game started'), ".
            "(1, 'correct', 'That was a correct guess'), ".
            "(1, 'incorrect', 'That was a incorrect guess'), ".
            "(1, 'victory', 'Congratulations, you escaped the gallows!'), ".
            "(1, 'defeat', 'You are just hanging there'), ".
            "(2, 'new', 'Nova igra je počela'), ".
            "(2, 'correct', 'To je bio točan pokušaj'), ".
            "(2, 'incorrect', 'To je bio netočan pokušaj'), ".
            "(2, 'victory', 'Čestitamo, izbjegli ste vješanje!'), ".
            "(2, 'defeat', 'Pravi ste obješenjak')"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DELETE from messages WHERE for IN ('correct', 'incorrect', 'new', 'vitory', 'defeat')" .
            " AND (language_id = 1 OR lanhuage_id = 2)");
    }
};
