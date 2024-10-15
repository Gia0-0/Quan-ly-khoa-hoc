<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Post
 *
 * @mixin Builder
 */
class Wishlist extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'wishlists';
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    protected $hidden = [];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
