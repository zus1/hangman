<?php

namespace App\Http\Controllers\Hangman;

use App\Dto\HangmanResponseDto;
use App\Http\Requests\HangmanRequest;
use App\Repository\HangmanRepository;
use Illuminate\Http\JsonResponse;

class Create
{
    public function __construct(
        private HangmanRepository $repository,
    ){
    }

    public function __invoke(HangmanRequest $request): JsonResponse
    {
        $game = $this->repository->create($request->input('language', 'en'));

        return new JsonResponse(HangmanResponseDto::create($game));
    }
}
