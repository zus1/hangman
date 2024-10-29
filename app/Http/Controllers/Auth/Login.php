<?php

namespace App\Http\Controllers\Auth;

use App\Constant\RouteName;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class Login
{
    public function __invoke(UserRequest $request): RedirectResponse
    {
        if(Auth::attempt($request->only(['email', 'password']), $request->has('remember'))) {
            /** @var User $user */
            $user = Auth::user();

            return Redirect::route(RouteName::HANGMAN_START)->withCookie($user->apiKeyCookie());
        }


        return Redirect::back(RedirectResponse::HTTP_NOT_FOUND)
            ->withInput()
            ->withErrors('error', 'Incorrect email or password');
    }
}
