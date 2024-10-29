<?php

namespace App\Http\Controllers\Auth;

use App\Constant\RouteName;
use App\Http\Requests\UserRequest;
use App\Repository\UserRepository;
use App\Services\Mailer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class Register
{
    public function __construct(
        private UserRepository $repository,
        private Mailer $mailer,
    ){
    }

    public function __invoke(UserRequest $request): RedirectResponse
    {
        $user = $this->repository->register($request->input());

        $this->mailer->verify($user);

        return Redirect::route(RouteName::AUTH_LOGIN_FORM);
    }
}
