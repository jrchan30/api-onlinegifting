<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'name', 'description', 'price', 'stock',
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
        return $this->morphMany('App\Models\Category', 'categoriable');
    }

    public function images()
    {
        return $this->morphMany('App\Models\Image', 'imageable');
    }
}
