<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $type
 * @property array $blueprint
 * @property int $fields_num
 */
class TikBlueprint extends Model
{
    use HasFactory;

    public function casts(): array
    {
        return [
            'blueprint' => 'json',
        ];
    }
}
