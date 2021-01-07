<?php

namespace App\Models\Paid;

use Illuminate\Database\Eloquent\Model;

class PaidBox extends Model
{
    protected $table = 'paid_boxes';

    protected $fillable = [
        'box_id', 'user_id', 'name', 'path', 'url'
    ];

    public function transactions()
    {
        return $this->morphToMany('App\Models\Paid\Transaction', 'transactionable');
    }

    public function paidProducts()
    {
        return $this->morphMany('App\Models\Paid\PaidProduct', 'paid_productable');
    }
}
