<?php

namespace App\Constant;

abstract class Constant
{
    public static function values(): array
    {
        $rf = new \ReflectionClass(static::class);

        return array_values($rf->getConstants());
    }
}
