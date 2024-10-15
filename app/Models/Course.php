<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Post
 *
 * @mixin Builder
 */
class Course extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    protected $hidden = [];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_courses', 'course_id', 'user_id');
    }

    public function courseInfo(): HasMany
    {
        return $this->hasMany(CourseInfo::class, 'course_id');
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class, 'course_id')->orderBy('priority');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function userCourses(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_courses', 'course_id', 'user_id')
            ->using(UserCourse::class);
    }

    public function tagCourses(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_courses', 'course_id', 'tag_id')
            ->using(TagCourse::class);
    }

    public function wishlistCourses(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlists', 'course_id', 'user_id')
            ->using(Wishlist::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->select(['id', 'email', 'user_type']);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
