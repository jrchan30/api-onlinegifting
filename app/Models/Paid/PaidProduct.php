<?php

namespace App\Models\Paid;

use Illuminate\Database\Eloquent\Model;

class PaidProduct extends Model
{
    protected $table = 'paid_products';

    protected $fillable = [
        'name', 'description', 'price', 'quantity', 'path', 'url'
    ];

    public function paidProductable()
    {
        return $this->morphTo();
    }
}
