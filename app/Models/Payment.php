<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * Post
 *
 * @mixin Builder
 */
class Payment extends Model
{
    use HasFactory;
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    protected $hidden = [];
}
