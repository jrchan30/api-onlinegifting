<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'rooms';

    protected $fillable = [
        'user_id', 'admin_id'
    ];

    public function admin()
    {
        return $this->hasOne(User::class, 'admin_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
