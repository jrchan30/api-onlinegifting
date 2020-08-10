<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'images';

    protected $fillable = [
        'path', 'url', 'imageable_id', 'imageable_type',
    ];

    public function imageable()
    {
        return $this->morphTo();
    }
}
