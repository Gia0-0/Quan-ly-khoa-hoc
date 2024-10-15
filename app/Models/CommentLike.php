<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Post
 *
 * @mixin Builder
 */
class CommentLike extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'comment_likes';
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    protected $hidden = [];
}
