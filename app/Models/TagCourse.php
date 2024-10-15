<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Query\Builder;

/**
 * Post
 *
 * @mixin Builder
 */
class TagCourse extends Pivot
{
    use HasFactory;
    protected $table = 'tag_courses';
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    protected $hidden = [];
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'tag_courses','tag_id', 'course_id');
    }
}
