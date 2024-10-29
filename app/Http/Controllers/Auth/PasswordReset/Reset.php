<?php

namespace App\Http\Controllers\Auth\PasswordReset;

use App\Constant\RouteName;
use App\Http\Requests\UserRequest;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Reset
{
    public function __construct(
        private UserRepository $userRepository,
        private TokenRepository $tokenRepository,
    ){
    }

    public function __invoke(UserRequest $request): RedirectResponse
    {
        try {
            $token = $this->tokenRepository->findByTokenString($request->input('token'));
            $user = $this->userRepository->findByTokenOr404($token);
        } catch(HttpException $e) {
            return Redirect::back()->withErrors('error', $e->getMessage());
        }

        $this->userRepository->changePassword($user, $request->input('password'));
        $this->tokenRepository->deactivate($token);

        return Redirect::route(RouteName::AUTH_LOGIN_FORM);
    }
}
