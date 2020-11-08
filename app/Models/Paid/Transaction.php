<?php

namespace App\Models\Paid;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'user_id', 'transaction_number', 'payment_type', 'receiver_full_address',
        'receiver_destination_code',
        'total_weight',
        'receiver_phone_number',
        'delivery_courier_code',
        'delivery_courier_service',
        'delivery_fee',
        'total_price',
        'arrival_date'
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
