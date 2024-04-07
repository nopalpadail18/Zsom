<?php

namespace App\Models;

use App\Models\User;
use App\Models\Groups;
use App\Models\PostAttachments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Posts extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['body', 'user_id', 'group_id', 'preview', 'preview_url'];
    protected $casts = [
        'preview' => 'json',
    ];

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

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reactions::class, 'object');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comments::class, 'post_id', 'id')->latest();
    }
    public function latest5Comments(): HasMany
    {
        return $this->hasMany(Comments::class, 'post_id', 'id');
    }

    public static function postForTimeLine($userId): Builder
    {
        return   Posts::query()
            ->withCount('reactions')
            ->with([
                'comments' => function ($q) {
                    $q->withCount('reactions');
                },
                'reactions' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }
            ])->latest();
    }

    public function isOwner($userId)
    {
        return $this->user_id == $userId;
    }
}
