<?php

namespace App\Models;

use App\Models\Paid\PaidBox;
use App\Models\Paid\PaidBundle;
use App\Models\Paid\PaidProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'user_id', 'body',
        'paid_product_id', 'paid_bundle_id',
        'reviewable_id', 'rating', 'reviewable_type'
    ];

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paidBundle()
    {
        return $this->belongsTo(PaidBundle::class);
    }

    public function paidProduct()
    {
        return $this->belongsTo(PaidProduct::class);
    }
}
