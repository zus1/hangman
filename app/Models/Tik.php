<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property int $id
 * @property array $grid
 * @property int $starts
 * @property int $fields_num
 * @property int $streak_length
 */
class Tik extends Game
{
    use HasFactory;

    public $timestamps = false;

    private bool $isStreak = false;

    public function casts(): array
    {
        return [
            'grid' => 'array',
        ];
    }

    public function setIsStreak(): void
    {
        $this->isStreak = true;
    }

    public function isStreakOnce(): bool
    {
        $isStreak = $this->isStreak;

        $this->isStreak = false;

        return $isStreak;
    }

    public function isStreak(): bool
    {
        return $this->isStreak;
    }
}
