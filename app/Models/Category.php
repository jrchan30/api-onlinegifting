<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name', 'category_id'
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

    public function subCategories()
    {
        return $this->hasMany(Category::class);
    }

    public function allSubCategories()
    {
        return $this->hasMany(Category::class)->with('subCategories');
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
