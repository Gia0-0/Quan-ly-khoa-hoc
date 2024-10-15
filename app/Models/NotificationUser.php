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
class NotificationUser extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $table = 'Notification_users';
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $guarded = [];
}
