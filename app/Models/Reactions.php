<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reactions extends Model
{
    use HasFactory;

    const UPDATED_AT = null;
    protected $table = 'post_reactions';
    protected $fillable = [
        'post_id',
        'user_id',
        'type',
    ];
}
