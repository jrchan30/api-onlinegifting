<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bundle extends Model
{
    use SoftDeletes;

    protected $table = 'bundles';

    protected $fillable = [
        'user_id', 'name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->morphToMany('App\Models\Transaction', 'transactionable');
    }

    public function detail()
    {
        return $this->morphOne('App\Models\Detail', 'detailable');
    }

    public function products()
    {
        return $this->morphToMany('App\Models\Product', 'productable');
    }

    public function reviews()
    {
        return $this->morphMany('App\Models\Review', 'reviewable');
    }

    public function discussions()
    {
        return $this->morphMany('App\Models\Discussion', 'discussionable');
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
