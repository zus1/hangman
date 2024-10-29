<?php

namespace App\Events;

use App\Models\Hangman;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HangmanUpdatingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        private Hangman $hangman,
    ){
    }

    public function getGame(): Hangman
    {
        return $this->hangman;
    }
}
