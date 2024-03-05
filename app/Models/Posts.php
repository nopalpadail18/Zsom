<?php

namespace App\Models;

use App\Models\User;
use App\Models\Groups;
use App\Models\PostAttachments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Posts extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['body', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Groups::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PostAttachments::class, 'post_id', 'id')->latest();
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reactions::class, 'post_id', 'id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comments::class, 'post_id', 'id')->latest();
    }
    public function latest5Comments(): HasMany
    {
        return $this->hasMany(Comments::class, 'post_id', 'id');
    }
}
