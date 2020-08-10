<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Bundle;
use Faker\Generator as Faker;

$factory->define(Bundle::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'name' => $faker->word . ' Bundle',
        // 'price' => 0,
    ];
});
