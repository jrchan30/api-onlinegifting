<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\UserDetail;
use Faker\Generator as Faker;

$factory->define(UserDetail::class, function (Faker $faker) {
    return [
        'type' => 'customer',
        'address' => $faker->address,
        'phone_num' => $faker->phoneNumber,
    ];
});
