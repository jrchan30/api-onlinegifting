<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productable extends Model
{
    protected $table = 'productables';

    protected $fillable = [
        'product_id', 'productable_id', 'productable_type', 'quantity'
    ];
}
