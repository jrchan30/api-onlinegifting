<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Cart extends Model
{

    protected $table = 'carts';

    protected $fillable = [
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bundles()
    {
        return $this->morphedByMany('App\Models\Bundle', 'cartable');
    }

    public function boxes()
    {
        return $this->morphedByMany('App\Models\Box', 'cartable');
    }
}
