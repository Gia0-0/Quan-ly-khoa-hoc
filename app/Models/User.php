<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Post
 *
 * @mixin Builder
 */
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function userCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'user_courses', 'course_id', 'user_id')
            ->using(UserCourse::class);
    }
    public function notifications() : BelongsToMany
    {
        return $this->belongsToMany(Notification::class, 'notification_users', 'user_id', 'notification_id');
    }
    public function wishlistCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'wishlists', 'course_id', 'user_id')
            ->using(Wishlist::class);
    }
    public function cartCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'carts', 'course_id', 'user_id')
            ->using(Cart::class);
    }
    public function reviewCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'reviews', 'course_id', 'user_id')
            ->using(Cart::class);
    }
    public function userDetail(): BelongsTo
    {
        return $this->belongsTo(UserDetail::class, 'id', 'user_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function wishlist(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cart(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
}
