<?php

namespace App\Models\Paid;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'user_id', 'transaction_number', 'payment_type', 'total_price',
        'receiver_location', 'arrival_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paidBundles()
    {
        return $this->morphedByMany('App\Models\Paid\PaidBundle', 'transactionable');
    }

    public function paidBoxes()
    {
        return $this->morphedByMany('App\Models\Paid\PaidBox', 'transactionable');
    }
}
