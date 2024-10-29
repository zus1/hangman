<?php

namespace App\Repository;

use App\Constant\DateTime;
use App\Constant\TokenType;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserRepository extends BaseRepository
{
    public function __construct(
        private TokenRepository $tokenRepository,
    ){
    }

    protected const MODEL = User::class;

    public function register(array $data): User
    {
        $user = new User();
        $user->email = $data['email'];
        $user->nickname = $data['nickname'];
        $user->password = Hash::make($data['password']);
        $user->active = true;

        $user->save();

        return $user;
    }

    public function findByTokenOr404(Token $token)
    {
        $user = $token->user()->first();

        if($user === null) {
            throw  new HttpException(404, 'User not found');
        }

        return $user;
    }

    public function verifyEmail(User $user): User
    {
        $user->email_verified_at = Carbon::now()->format(DateTime::FORMAT);

        $user->save();

        return $user;
    }

    public function changePassword(User $user, string $password): User
    {
        $user->password = Hash::make($password);

        $user->save();

        return $user;
    }
}
