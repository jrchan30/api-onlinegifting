<?php

namespace App\Models;

use App\Models\Paid\PaidBundle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bundle extends Model
{
    use SoftDeletes;

    protected $table = 'bundles';

    protected $fillable = [
        'user_id', 'name', 'description',
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
        return $this->morphToMany('App\Models\Product', 'productable')->withPivot('quantity');
    }

    public function reviews()
    {
        return $this->morphMany('App\Models\Review', 'reviewable');
    }

    public function discussions()
    {
        return $this->morphMany('App\Models\Discussion', 'discussionable');
    }

    public function likes()
    {
        return $this->morphMany('App\Models\Like', 'likeable');
    }

    public function paidBundles()
    {
        return $this->hasMany(PaidBundle::class);
    }

    public function calculatePrice()
    {
        $calculated = $this->products->sum(function ($products) {
            return $products->price;
        });
        return $calculated;
    }

    public function calculateWeight()
    {
        $calculated = $this->products->sum(function ($products) {
            return $products->weight;
        });
        return $calculated;
    }

    public function avgRating()
    {
        $reviews = $this->reviews;
        $count = $reviews->count();

        $totalRate = $reviews->sum(function ($x) {
            return $x->rating;
        });

        return $count > 0 ? $totalRate / $count : null;
    }
}
