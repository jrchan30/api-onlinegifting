<?php

namespace App\Models\Paid;

use App\Models\Bundle;
use App\Models\Review;
use Illuminate\Database\Eloquent\Model;

class PaidBundle extends Model
{
    protected $table = 'paid_bundles';

    protected $fillable = [
        'bundle_id', 'name', 'path', 'url'
    ];

    public function transactions()
    {
        return $this->morphToMany('App\Models\Paid\Transaction', 'transactionable');
    }

    public function paidProducts()
    {
        return $this->morphMany('App\Models\Paid\PaidProduct', 'paid_productable');
    }

    public function bundle()
    {
        return $this->belongsTo(Bundle::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
