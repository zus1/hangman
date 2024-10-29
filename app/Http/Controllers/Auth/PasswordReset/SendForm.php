<?php

namespace App\Http\Controllers\Auth\PasswordReset;

use Illuminate\Support\Facades\View;

class SendForm
{
    public function __invoke(): \Illuminate\View\View
    {
        return View::make('auth.reset-password-send');
    }
}
