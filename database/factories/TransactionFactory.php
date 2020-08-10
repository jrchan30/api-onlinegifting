<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Transaction;
use Faker\Generator as Faker;

$factory->define(Transaction::class, function (Faker $faker) {
    return [
        'delivery_fee' => $faker->randomNumber(5),
        'total_price' => $faker->randomNumber(8),
        'receiver_location' => $faker->latitude . ' ' . $faker->longitude,
        'arrival_date' => $faker->dateTimeBetween('now', '+2 years'),
    ];
});
