<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property  int $id
 * @property string $short
 * @property string $language
 */
class Language extends Model
{
    use HasFactory;

    public $timestamps = false;
}
