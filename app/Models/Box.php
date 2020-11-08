<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Box extends Model
{
    use SoftDeletes;

    protected $table = 'boxes';

    protected $fillable = [
        'user_id', 'name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function carts()
    {
        return $this->morphToMany('App\Models\Cart', 'cartable');
    }

    public function detail()
    {
        return $this->morphOne('App\Models\Detail', 'detailable');
    }

    public function products()
    {
        return $this->morphToMany('App\Models\Product', 'productable');
    }

    public function boxProductQuantities()
    {
        return $this->hasMany(BoxProductQuantity::class);
    }

    // public function calculatePrice()
    // {
    //     $calculated = $this->products->sum(function ($products) {
    //         return $products->price;
    //     });

    //     $this->price = $calculated;
    //     $this->save();
    // }
}
