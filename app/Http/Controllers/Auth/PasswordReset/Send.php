<?php

namespace App\Http\Controllers\Auth\PasswordReset;

use App\Http\Requests\UserRequest;
use App\Services\Mailer;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Send
{
    public function __construct(
        private Mailer $mailer,
    ){
    }

    public function __invoke(UserRequest $request): RedirectResponse
    {
        $this->mailer->resetPassword($request->input('email'));

        return Redirect::back()->with('message', 'Email sent, please check your mailbox')->withInput();
    }
}
