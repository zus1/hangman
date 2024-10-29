<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\View;

class LoginForm
{
    public function __invoke(): \Illuminate\View\View
    {
        return View::make('auth.login');
    }
}
