<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name', 'categoriable_id', 'categoriable_type',
    ];

    public function categoriable()
    {
        return $this->morphTo();
    }
}
