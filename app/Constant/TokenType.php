<?php

namespace App\Constant;

use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TokenType
{
    public const RESET_PASSWORD = 'reset-password';
    public const VERIFY_EMAIL = 'verify-email';
    public const API_KEY = 'api_key';

    public static function expiresAt(Carbon $createdAt, string $type): string
    {
        return match ($type) {
            self::VERIFY_EMAIL,
            self::RESET_PASSWORD => $createdAt->addDays(5)->format(DateTime::FORMAT),
            self::API_KEY => $createdAt->addMonth()->format(DateTime::FORMAT),
            default => throw new HttpException(500, 'Unknown token type '.$type),
        };
    }

    public static function length(string $type): int
    {
        return match ($type) {
            self::VERIFY_EMAIL,
            self::RESET_PASSWORD => 50,
            self::API_KEY => 100,
            default => throw new HttpException(500, 'Unknown token type '.$type),
        };
    }
}
