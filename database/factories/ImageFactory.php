<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Image;
use Faker\Generator as Faker;

$factory->define(Image::class, function (Faker $faker) {
    return [
        'path' => $faker->image(),
        'url' => $faker->imageUrl(),
    ];
});
