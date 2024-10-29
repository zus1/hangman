<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\View;

class RegisterForm
{
    public function __invoke(): \Illuminate\View\View
    {
        return View::make('auth.register');
    }
}
