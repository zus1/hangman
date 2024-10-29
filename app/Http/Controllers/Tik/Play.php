<?php

namespace App\Http\Controllers\Tik;

use App\Models\Tik;
use App\Services\TikGame;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Play
{
    public function __construct(
        private TikGame $game,
    ){
    }

    public function __invoke(Request $request, Tik $tik): JsonResponse
    {
        $tik = $this->game->play($request->input(), $tik);

        return new JsonResponse($tik);
    }
}
