<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $table = 'details';

    protected $fillable = [
        'colour', 'design', 'detailable_id', 'detailable_type',
    ];

    public function categories()
    {
        return $this->morphToMany('App\Models\Category', 'categoriable');
    }

    public function detailable()
    {
        return $this->morphTo();
    }

    public function image()
    {
        return $this->morphOne('App\Models\Image', 'imageable');
    }
}
