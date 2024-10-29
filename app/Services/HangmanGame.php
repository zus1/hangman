<?php

namespace App\Services;

use App\Models\Hangman;
use App\Repository\HangmanRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HangmanGame
{
    public function __construct(
        private HangmanRepository $repository,
    ){
    }

    public function play(array $data, Hangman $game): Hangman
    {
        if(isset($data['word'])) {
            return $this->resolveWord($data['word'], $game);
        }

        if(isset($data['letter'])) {
            return $this->resolveLetter($data['letter'], $game);
        }

        throw new HttpException(400, 'Word or letter not sent');
    }

    private function resolveLetter(string $letter, Hangman $game): Hangman
    {
        if(in_array($letter, $game->guesses)) {
            return $this->repository->updateMistake($game);
        }

        $pos = -1;
        $newGuesses = [];
        while(($pos = mb_stripos($game->word, $letter, $pos + 1)) !== false) {
            $newGuesses[$pos] = $letter;
        }

        if($newGuesses === []) {
            return $this->repository->updateMistake($game);
        }

        return $this->repository->updateCorrect($game, $newGuesses);
    }

    private function resolveWord(string $word, Hangman $game): Hangman
    {
        if($game->word === $word) {
            return $this->repository->updateDirectlyGuessedWord($game);
        }

        return $this->repository->updateMistake($game);
    }
}
