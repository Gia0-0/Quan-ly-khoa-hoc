<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder;

/**
 * Post
 *
 * @mixin Builder
 */
class Notification extends Model
{
    use HasFactory;
    protected $table = 'Notifications';
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $guarded = [];

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notification_users', 'notification_id', 'user_id');
    }

}
