<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Discount extends Model
{
    use HasFactory;
    protected $table = 'discounts';
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    protected $hidden = [];

    public function discountCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'categories', 'discount_id', 'category_id')
            ->using(DiscountCategory::class);
    }
}
