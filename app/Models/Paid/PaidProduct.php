<?php

namespace App\Models\Paid;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Eloquent\Model;

class PaidProduct extends Model
{
    protected $table = 'paid_products';

    protected $fillable = [
        'product_id', 'name', 'description', 'price', 'quantity', 'weight', 'path', 'url'
    ];

    public function paidProductable()
    {
        return $this->morphTo();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
