<?php

namespace App\Models;

// use Illuminate\Auth\Notifications\VerifyEmail;

use App\Models\Paid\Transaction;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmail;
use App\Notifications\ResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use SoftDeletes, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function boxes()
    {
        return $this->hasMany(Box::class);
    }

    public function bundles()
    {
        return $this->hasMany(Bundle::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
    }

    //cek import notificaiton resetPassword keknya masih salah
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
}
