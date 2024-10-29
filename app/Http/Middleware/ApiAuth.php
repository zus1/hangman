<?php

namespace App\Http\Middleware;

use App\Constant\RouteName;
use App\Models\Token;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiAuth
{
    public function __construct(
        private UserRepository $userRepository,
        private TokenRepository $tokenRepository,
        private Guard $guard,
        private Encrypter $encrypter,
    ){
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->getToken($request);

        if($token === null) {
            return $next($request);
        }

        $user = $this->userRepository->findByTokenOr404($token);

        $this->guard->setUser($user);

        return $next($request);
    }

    private function getToken(Request $request): ?Token
    {
        $encrypted = $request->header('Authorization');
        if($encrypted === null) {
            if(in_array($request->route()->action['as'], $this->sometimesAuthenticated())) {
                return null;
            }

            throw new HttpException(401, 'No api key sent');
        }

        $tokenStr = explode('|', $this->encrypter->decrypt($encrypted, false))[1];

        return $this->tokenRepository->findByTokenString($tokenStr);
    }

    private function sometimesAuthenticated(): array
    {
        return [
            RouteName::HANGMAN_CREATE,
            RouteName::TIK_CREATE,
        ];
    }
}
