<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Cart extends Model
{
    use SoftDeletes;

    protected $table = 'carts';

    protected $fillable = [
        'user_id', 'total_price', 'receiver_location', 'arrival_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bundles()
    {
        return $this->morphedByMany('App\Models\Bundle', 'cartable');
    }

    public function boxes()
    {
        return $this->morphedByMany('App\Models\Box', 'cartable');
    }
}
