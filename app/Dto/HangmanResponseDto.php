<?php

namespace App\Dto;

use App\Models\Hangman;

class HangmanResponseDto implements \JsonSerializable
{
    private array $response;

    public static function create(Hangman $hangman): self
    {
        $instance = new self();

        $gameArr = $hangman->toArray();
        $gameArr['message'] = $hangman->message;
        $gameArr['word_length'] = mb_strlen($hangman->word);

        $instance->response = $gameArr;

        return $instance;
    }

    public function jsonSerialize(): array
    {
        return $this->response;
    }
}
