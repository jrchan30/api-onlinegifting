<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    protected $table = 'discussions';

    protected $fillable = [
        'body', 'discussionable_id', 'discussionable_type'
    ];

    public function discussionable()
    {
        return $this->morphTo();
    }
}
