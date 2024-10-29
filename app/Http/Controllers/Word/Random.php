<?php

namespace App\Http\Controllers\Word;

use App\Repository\WordRepository;
use Illuminate\Http\JsonResponse;

class Random
{
    public function __construct(
        private WordRepository $repository,
    ){
    }

    public function __invoke(): JsonResponse
    {
        $word = $this->repository->findRandom();

        return new JsonResponse(collect($word)->only('word')->toArray());
    }
}
