<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Post
 *
 * @mixin Builder
 */
class Lesson extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'lessons';
    protected $fillable = [];
    protected $guarded = [];
    protected $hidden = [];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }
}
