<?php

namespace App\Http\Controllers\Tik;
use App\Repository\TikBlueprintRepository;
use Illuminate\Support\Facades\View;

class Start
{
    public function __construct(
        private TikBlueprintRepository $tikBlueprintRepository,
    ){
    }

    public function __invoke(): \Illuminate\View\View
    {
        $possibleGrids = $this->tikBlueprintRepository->findFieldsNum();

        return View::make('tik', ['possibleGrids' => $possibleGrids]);
    }
}
