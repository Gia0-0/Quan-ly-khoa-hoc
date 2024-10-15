<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Post
 *
 * @mixin Builder
 */
class UserCourse extends Pivot
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'user_courses';
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    protected $hidden = [];
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'user_courses','user_id', 'course_id');
    }
}
