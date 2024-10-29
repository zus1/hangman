<?php

namespace App\Http\Controllers\Auth;

use App\Models\Token;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Verify
{
    public function __construct(
        private UserRepository $userRepository,
        private TokenRepository $tokenRepository,
    ){
    }

    public function __invoke(Request $request): \Illuminate\View\View
    {
        try {
            $token = $this->tokenRepository->findByTokenString($request->input('token', ''));
            $user = $this->userRepository->findByTokenOr404($token);
        } catch(HttpException $e) {
            return View::make('auth.email-verified')
                ->with('error', sprintf('Email could not be verified : %s', $e->getMessage()));
        }

        $this->userRepository->verifyEmail($user);
        $this->tokenRepository->deactivate($token);

        return View::make('auth.email-verified')->with('success', 'Email successfully verified');
    }
}
