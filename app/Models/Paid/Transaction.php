<?php

namespace App\Models\Paid;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'transaction_number',
        'receiver_phone_number',
        'receiver_full_address',
        'receiver_city',
        'receiver_postal_code',
        'receiver_destination_code',
        'total_weight',
        'delivery_courier_code',
        'delivery_courier_service',
        'delivery_fee',
        'total_price',
        'arrival_date',
        'status',
        'payment_type',

        'token',
        'payloads',
        'payment_type',
        'va_number',
        'vendor_name',
        'biller_code',
        'bill_key'
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
