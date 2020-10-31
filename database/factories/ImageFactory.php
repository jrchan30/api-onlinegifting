<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Image;
use Faker\Generator as Faker;

$factory->define(Image::class, function (Faker $faker) {

    $baseUrl = "https://picsum.photos/";
    $id = random_int(1, 100);
    $url = "id/" . $id . "/1280/720/";

    return [

        'path' => $url,
        'url' => $baseUrl . $url,
    ];
});
