<?php

namespace App\Http\Controllers\Auth;

use App\Constant\RouteName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class Logout
{
    public function __invoke(): RedirectResponse
    {
        Auth::logout();

        return Redirect::route(RouteName::HANGMAN_START);
    }
}
