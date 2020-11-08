<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker->word . ' Product',
        'description' => $faker->sentence(),
        'price' => $faker->randomNumber(6),
        'stock' => $faker->randomNumber(2),
        'weight' => $faker->randomNumber(3),
    ];
});
