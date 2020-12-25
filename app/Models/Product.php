<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    // protected $appends = ['avg_rate'];

    protected $fillable = [
        'name', 'description', 'price', 'stock', 'weight'
    ];

    public function bundles()
    {
        return $this->morphedByMany('App\Models\Bundle', 'productable');
    }

    public function boxes()
    {
        return $this->morphedByMany('App\Models\Box', 'productable');
    }

    public function categories()
    {
        return $this->morphToMany('App\Models\Category', 'categoriable');
    }

    public function images()
    {
        return $this->morphMany('App\Models\Image', 'imageable');
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

    public function avgRating()
    {
        $reviews = $this->reviews;
        $count = $reviews->count();

        $totalRate = $reviews->sum(function ($x) {
            return $x->rating;
        });

        return $count > 0 ?  $totalRate / $count : null;
    }

    public function boxProductQuantities()
    {
        return $this->hasMany(BoxProductQuantity::class);
    }
}
