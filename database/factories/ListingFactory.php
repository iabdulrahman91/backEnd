<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Listing;
use Faker\Generator as Faker;

$factory->define(Listing::class, function (Faker $faker) {
    $item = [
        'company' => $faker->company,
        'category' => $faker->text(7),
        'product' => $faker->text(6),
    ];

    $location = [
        'lat' => $faker->latitude,
        'lng' => $faker->longitude,
    ];

    $days = [
        "01-02-2019" => true,
        "02-02-2019" => true,
        "03-02-2019" => true,
        "04-02-2019" => true,
        "05-02-2019" => true,
        "06-02-2019" => true,
        "07-02-2019" => true,
    ];

    return [
        'item' => json_encode($item),
        'location' => json_encode($location),
        'days' => json_encode($days),
        'price' => $faker->randomFloat(2,1,1000),
    ];

});
