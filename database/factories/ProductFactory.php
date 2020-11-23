<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Product;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    $rand = rand(1, 10);
    $deletedAt = null;
    $createdAt = Carbon::now();
    if ($rand == 1) {
        $deletedAt = Carbon::now()->addMinutes(13);
    }
    $createdAt = Carbon::now()->subMinutes(rand(10, 10000));

    return [
        'name' => $faker->word . ' Product',
        'description' => $faker->sentence(),
        'price' => $faker->randomNumber(6),
        'stock' => $faker->randomNumber(2),
        'weight' => $faker->randomNumber(3),
        'created_at' => $createdAt,
        'deleted_at' => $deletedAt
    ];
});
