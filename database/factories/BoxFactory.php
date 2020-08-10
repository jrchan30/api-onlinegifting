<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Box;
use Faker\Generator as Faker;

$factory->define(Box::class, function (Faker $faker) {
    return [
        'name' => $faker->word . ' Box',
        // 'price' => 0,
    ];
});
