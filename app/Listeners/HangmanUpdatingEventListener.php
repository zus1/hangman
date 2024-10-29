<?php

namespace App\Listeners;

use App\Events\HangmanUpdatingEvent;
use App\Models\Game;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HangmanUpdatingEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(HangmanUpdatingEvent $event): void
    {
        $game = $event->getGame();

        $game->guesses = $this->shuffleByKey($game->guesses);
    }

    private function shuffleByKey(array $toShuffle): array
    {
        $keys = array_keys($toShuffle);
        shuffle($keys);

        $newGuesses = [];
        foreach ($keys as $key) {
            $newGuesses[$key] = $toShuffle[$key];
        }

        return $newGuesses;
    }
}
