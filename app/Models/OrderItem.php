<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;

/**
 * Post
 *
 * @mixin Builder
 */
class OrderItem extends Model
{
    use HasFactory;
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    protected $hidden = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
