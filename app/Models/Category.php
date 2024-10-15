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
class Category extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'categories';

    protected $primaryKey = 'id';

    protected $fillable = [];
    protected $guarded = [];
    protected $hidden = [];

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'category_id');
    }
}
