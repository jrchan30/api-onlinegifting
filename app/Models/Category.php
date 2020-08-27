<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
    ];

    public function categoriable()
    {
        return $this->morphTo();
    }

    public function details()
    {
        return $this->morphedByMany('App\Models\Detail', 'categoriable');
    }

    public function products()
    {
        return $this->morphedByMany('App\Models\Product', 'categoriable');
    }
}
