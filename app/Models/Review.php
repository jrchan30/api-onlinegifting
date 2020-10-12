<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'body', 'reviewable_id', 'rating', 'reviewable_type'
    ];

    public function reviewable()
    {
        return $this->morphTo();
    }
}
