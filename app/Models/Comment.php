<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Query\Builder;

/**
 * Post
 *
 * @mixin Builder
 */
class Comment extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'comments';
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    protected $hidden = [];
    protected $appends = ['likes_count'];

    protected static function boot(): void
    {
        parent::boot();

        // Khi một comment bị xóa, xóa tất cả các bình luận con và likes của nó
        static::deleting(function ($comment) {
            // Xóa tất cả các lượt thích liên quan đến bình luận này
            $comment->likes()->delete();

            // Xóa tất cả các bình luận con của nó
            $comment->children()->each(function ($child) {
                $child->delete();
            });
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->with([
            'children' => function ($query) {
                $query->with(['user:id,email', 'user.userDetail:id,user_id,family_name,given_name,image_path,image_name']);
            },
            'user:id,email',
            'user.userDetail:id,user_id,family_name,given_name,image_path,image_name'
        ]);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(CommentLike::class);
    }
    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }
}
