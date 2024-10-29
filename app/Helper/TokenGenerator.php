<?php

namespace App\Helper;

use App\Constant\TokenType;

class TokenGenerator
{
    private string $characters = 'abcdefgahjuiABCDERTGJKLIO1234567890';

    public function generate(int $length): string
    {
        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $token .= $this->characters[random_int(0, strlen($this->characters) - 1)];
        }

        return $token;
    }

    public function isToken(string $toCheck, string $type): bool
    {
        $length = TokenType::length($type);

        if(strlen($toCheck) !== $length) {
            return false;
        }

        for($i = 0, $max = strlen($toCheck); $i < $max; $i++) {
            if(str_contains($this->characters, $toCheck[$i]) === false) {
                return false;
            }
        }

        return true;
    }
}
