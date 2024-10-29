<?php

namespace App\Models;

use App\Events\HangmanUpdatingEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property bool $is_finished
 * @property string $result
 * @property int $user_id
 */
class Game extends Discriminator
{
    use HasFactory;

    protected const PARENT = self::class;

    protected $hidden = [
        'created_at',
        'updated_at',
        'user',
        'user_id'
    ];

    public string $message = '';

    public function casts(): array
    {
        return [
            'is_finished' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
