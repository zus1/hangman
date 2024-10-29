<?php

namespace App\Models;

use App\Constant\DateTime;
use App\Constant\TokenType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $nickname
 * @property bool $active
 * @property string $email_verified_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class, 'user_id', 'id');
    }

    public function apiKeyCookie(): Cookie
    {
        /** @var Token $apiKey */
        $apiKey = $this->tokens()
            ->where('type', TokenType::API_KEY)
            ->where('active', true)
            ->where('expires_at', '>=', Carbon::now()->format(DateTime::FORMAT))
            ->first();

        if($apiKey === null) {
            throw new HttpException(500, 'Api key not found');
        }

        return new Cookie(
            name: strtolower(config('app.name')).'_api_key',
            value: $apiKey->token,
            expire: new Carbon($apiKey->expires_at),
            httpOnly: false
        );
    }
}
