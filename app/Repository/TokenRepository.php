<?php

namespace App\Repository;

use App\Constant\DateTime;
use App\Constant\TokenType;
use App\Helper\TokenGenerator;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TokenRepository extends BaseRepository
{
    public function __construct(
        private TokenGenerator $tokenGenerator,
    ){
    }

    protected const MODEL = Token::class;

    public function create(User $user, string $type): Token
    {
        $createdAt = Carbon::now();

        $token = new Token();
        $token->token = $this->tokenGenerator->generate(TokenType::length($type));
        $token->created_at = $createdAt->format(DateTime::FORMAT);
        $token->expires_at = TokenType::expiresAt($createdAt, $type);
        $token->type = $type;
        $token->active = true;

        $token->user()->associate($user);

        $token->save();

        return $token;
    }

    public function findByTokenString(string $token): Token
    {
        /** @var Token $token */
        $token = $this->findOneByOr404(['token' => $token]);

        if($token->active === false) {
            throw new HttpException(404, 'Token inactive');
        }

        if($token->expires_at < Carbon::now()->format(DateTime::FORMAT)) {
            throw new HttpException(404, 'Token expired');
        }

        return $token;
    }

    public function deactivate(string|Token $token): Token
    {
        $token = is_string($token) ? $this->findOneBy(['token' => $token]) : $token;

        $token->active = false;

        $token->save();

        return $token;
    }

    public function deactivateAll(User $user, string $type): void
    {
        $builder = $this->getBuilder();

        $builder->where('type', $type)
            ->whereRelation('user', 'id', $user->id)
            ->update(['active' => false]);
    }
}
