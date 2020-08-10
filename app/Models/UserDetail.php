<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $table = 'user_details';

    protected $fillable = [
        'user_id', 'type', 'address', 'phone_num',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function image()
    {
        return $this->morphOne('App\Models\Image', 'imageable');
    }
}
