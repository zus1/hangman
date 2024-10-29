<?php

namespace App\Services;

use App\Constant\AuthEmailType;
use App\Constant\TokenType;
use App\Mail\ResetPassword;
use App\Mail\Verification;
use App\Models\Token;
use App\Models\User;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\Mail;

class Mailer
{
    public function __construct(
        private TokenRepository $tokenRepository,
        private UserRepository $userRepository,
    ){
    }

    public function resend(string $identifier, string $type): bool
    {
        if($type === AuthEmailType::VERIFY) {
             return $this->verify(
                user: $this->userRepository->findByTokenOr404($this->tokenRepository->findByTokenString($identifier))
            );
        }
        if($type === AuthEmailType::RESET_PASSWORD) {
            return $this->resetPassword($identifier);
        }

        return false;
    }

    public function verify(User $user): bool
    {
        return $this->send(
            mailable: Verification::class,
            token: $this->tokenRepository->create($user, TokenType::VERIFY_EMAIL),
            user: $user
        );
    }

    public function resetPassword(string $email): bool
    {
        /** @var User $user */
        $user = $this->userRepository->findOneByOr404(['email' => $email]);

        return $this->send(
            mailable: ResetPassword::class,
            token: $this->tokenRepository->create($user, TokenType::RESET_PASSWORD),
            user: $user
        );
    }

    public function send(string $mailable, Token $token, User $user): bool
    {
        $sentMessage =  Mail::to($user->email)->send(new $mailable($user, $token));

        return $sentMessage?->getMessageId() !== '';
    }
}
