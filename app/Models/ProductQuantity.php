<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductQuantity extends Model
{
    protected $table = 'product_quantities';

    protected $fillable = [
        'product_id', 'quantity', 'product_quantitable_id', 'product_quantitable_type'
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productQuantitable()
    {
        return $this->morphTo();
    }
}
