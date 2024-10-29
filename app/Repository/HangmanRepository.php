<?php

namespace App\Repository;

use App\Constant\MessageKey;
use App\Constant\Result;
use App\Helper\ImageHelper;
use App\Models\Hangman;

class HangmanRepository extends GameRepository
{
    protected const MODEL = Hangman::class;

    private int $maxMistakes;

    public function __construct(
        private WordRepository $wordRepository,
        private ImageHelper $imageHelper,
        private MessageRepository $messageRepository,
    ){
        $this->maxMistakes = count(scandir(public_path('/images/hangman'))) - 3; // minus . , .. and first image
    }

    public function create(string $language): Hangman
    {
        $word = $this->wordRepository->findRandom($language);

        $hangman = new Hangman();
        $hangman->word = $word->word;
        $hangman->word_spaces = $word->getSpaces();
        $hangman->image = $this->imageHelper->get(numOfMistakes: 0);
        $hangman->message = $this->messageRepository->findByLanguage(MessageKey::NEW, $language)->message;
        $hangman->max_mistakes = $this->maxMistakes;
        $hangman->language = $language;

        $this->baseCreate($hangman);

        $hangman->save();

        return $hangman;
    }

    public function updateDirectlyGuessedWord(Hangman $Hangman): Hangman
    {
        $this->revealAllLetters($Hangman);

        return $this->updateGuessedWord($Hangman);
    }

    public function updateMistake(Hangman $Hangman): Hangman
    {
        $Hangman->mistakes += 1;
        $Hangman->image = $this->imageHelper->get($Hangman->mistakes);
        $Hangman->message = $this->messageRepository->findByLanguage(MessageKey::INCORRECT, $Hangman->language)->message;

        if($Hangman->mistakes >= $this->maxMistakes) {
            return $this->updateNotGuessedWord($Hangman);
        }

        $Hangman->save();

        return $Hangman;
    }

    public function updateCorrect(Hangman $Hangman, array $newGuesses): Hangman
    {
        $Hangman->guesses += $newGuesses;
        $Hangman->message = $this->messageRepository->findByLanguage(MessageKey::CORRECT, $Hangman->language)->message;

        if(count($Hangman->guesses) + count($Hangman->word_spaces) === mb_strlen($Hangman->word)) {
            return $this->updateGuessedWord($Hangman);
        }

        $Hangman->save();

        return $Hangman;
    }

    private function updateNotGuessedWord(Hangman $Hangman): Hangman
    {
        $this->revealAllLetters($Hangman);

        $this->updateFinished($Hangman, Result::NOT_GUESSED);
        $Hangman->message = $this->messageRepository->findByLanguage(MessageKey::DEFEAT, $Hangman->language)->message;

        $Hangman->save();

        return $Hangman;
    }

    private function updateGuessedWord(Hangman $Hangman): Hangman
    {
        $this->updateFinished($Hangman, Result::GUESSED);
        $Hangman->image = $this->imageHelper->getImage('victory.jpg');
        $Hangman->message = $this->messageRepository->findByLanguage(MessageKey::VICTORY, $Hangman->language)->message;

        $Hangman->save();

        return $Hangman;
    }

    private function revealAllLetters(Hangman $Hangman): void
    {
        $guesses = [];
        for($i = 0, $max = mb_strlen($Hangman->word); $i < $max; $i++) {
            $guesses[] = mb_substr($Hangman->word, $i, 1, encoding: 'UTF-8');
        }

        $Hangman->guesses = $guesses;
    }
}
