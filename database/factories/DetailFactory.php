<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Detail;
use Faker\Generator as Faker;

$factory->define(Detail::class, function (Faker $faker) {
    return [
        'colour' => $faker->hexColor,
    ];
});
