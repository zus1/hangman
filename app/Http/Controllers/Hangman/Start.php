<?php

namespace App\Http\Controllers\Hangman;

use App\Repository\LanguageRepository;
use Illuminate\Support\Facades\View;


class Start
{
    public function __construct(
        private LanguageRepository $languageRepository,
    ){
    }

    public function __invoke(): \Illuminate\View\View
    {
        $languages = $this->languageRepository->findAll();

        return View::make('game', [
            'languages' => $languages->all(),
        ]);
    }
}
