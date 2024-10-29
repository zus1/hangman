<?php

namespace App\Listeners;

use App\Constant\TokenType;
use App\Models\User;
use App\Repository\TokenRepository;
use Illuminate\Support\Facades\Cookie;

abstract class AuthEventListener
{
    protected string $cookieKey = '';

    public function __construct(
        protected TokenRepository $tokenRepository
    ){
        $this->cookieKey = strtolower(config('app.name')).'_api_key';
    }

    protected function expireApiKey(string $apiKey = null): void
    {
        $apiKey = $apiKey ?? Cookie::get($this->cookieKey);
        Cookie::expire($this->cookieKey);

        if($apiKey !== null) {
            $this->tokenRepository->deactivate($apiKey);
        }
    }

    protected function refreshApiKey(User $user): void
    {
        $apiKey = Cookie::get($this->cookieKey);

        if($apiKey !== null) {
            $this->expireApiKey($apiKey);
        }

        $this->tokenRepository->create($user, TokenType::API_KEY);
    }
}
