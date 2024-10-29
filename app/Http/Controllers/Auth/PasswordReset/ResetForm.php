<?php

namespace App\Http\Controllers\Auth\PasswordReset;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ResetForm
{
    public function __invoke(Request $request): \Illuminate\View\View
    {
        return View::make('auth.reset-password', ['token' => $request->input('token', '')]);
    }
}
