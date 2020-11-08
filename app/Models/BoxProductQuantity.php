<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoxProductQuantity extends Model
{
    protected $table = 'box_product_quantities';

    protected $fillable = [
        'box_id', 'product_id', 'quantity'
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function box()
    {
        return $this->belongsTo(Box::class);
    }
}
