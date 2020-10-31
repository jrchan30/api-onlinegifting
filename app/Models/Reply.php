<?php

namespace App\Models;

use App\Models\User;
use App\Models\Discussion;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $table = 'replies';

    protected $fillable = [
        'user_id', 'discussion_id', 'body'
    ];

    public function discussions()
    {
        return $this->belongsTo(Discussion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
