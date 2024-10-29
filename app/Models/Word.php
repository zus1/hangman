<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $word
 * @property string $created_at
 * @property bool $active
 * @property int $language_id
 */
class Word extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    public function getSpaces(): array
    {
        $sanitized = preg_replace('/\\s/', '$', $this->word);

        $spaces = [];

        $pos = -1;
        while(($pos = strpos($sanitized, '$', $pos + 1)) !== false) {
            $spaces[] = $pos;
        }

        return $spaces;
    }
}
