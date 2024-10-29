<?php

namespace App\Models;

use App\Events\HangmanUpdatingEvent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
* @property int $id
* @property string $word
* @property array $word_spaces
* @property string $image
* @property array $guesses
* @property int $mistakes
* @property int $max_mistakes
* @property string $language
*/
class Hangman extends Game
{
    use HasFactory;

    public $table = 'hangmans';

    public $timestamps = false;

    protected $hidden = [
        'word',
    ];

    public $dispatchesEvents = [
        'updating' => HangmanUpdatingEvent::class,
    ];

    public function casts(): array
    {
        return [
            'word_spaces' => 'array',
            'guesses' => 'array',
        ];
    }

    public function word(): Attribute
    {
        return Attribute::get(fn(string $value) => strtolower($value));
    }

    public function guesses(): Attribute
    {
        return new Attribute(
            get: fn (?string $value) => $value === null ? [] : json_decode($value, true),
        );
    }
}
