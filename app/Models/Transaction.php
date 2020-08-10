<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'user_id', 'total_price', 'receiver_location', 'arrival_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bundles()
    {
        return $this->morphedByMany('App\Models\Bundle', 'transactionable');
    }

    public function boxes()
    {
        return $this->morphedByMany('App\Models\Box', 'transactionable');
    }
}
