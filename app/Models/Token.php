<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $created_at
 * @property string $expires_at
 * @property string $token
 * @property string $type
 * @property bool $active
 */
class Token extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $hidden = [
        'updated_at'
    ];

    public function casts(): array
    {
        return [
            'active' => 'boolean',
            'created_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id', 'id');
    }
}
