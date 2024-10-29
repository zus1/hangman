<?php

namespace App\Http\Controllers\Me;

use App\Constant\Result;
use App\Models\User;
use App\Repository\GameRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class Games
{
    public function __construct(
        private GameRepository $repository,
    ){
    }

    public function __invoke(Request $request): \Illuminate\View\View
    {
        /** @var User $auth */
        $auth = Auth::user();

        $games = $this->repository->findForUser($request->input(), $auth);

        return View::make('history', [
            'filterResult' => Result::values(),
            'games' => $games
        ]);
    }
}
