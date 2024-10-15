<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Post
 *
 * @mixin Builder
 */
class Chapter extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'chapters';

    protected $primaryKey = 'id';

    protected $fillable = [];
    protected $guarded = [];
    protected $hidden = [];

    public function course():BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function lessons():HasMany
    {
        return $this->hasMany(Lesson::class, 'chapter_id')->orderBy('priority');
    }
}
