<?php

namespace App\Http\Controllers\Hangman;

use App\Dto\HangmanResponseDto;
use App\Http\Requests\HangmanRequest;
use App\Models\Game;
use App\Models\Hangman;
use Illuminate\Http\JsonResponse;

class Play
{
    public function __construct(
        private \App\Services\HangmanGame $play,
    ){
    }

    public function __invoke(HangmanRequest $request, Hangman $hangman): JsonResponse
    {
        $hangman = $this->play->play($request->input(), $hangman);

        return new JsonResponse(HangmanResponseDto::create($hangman));
    }
}
