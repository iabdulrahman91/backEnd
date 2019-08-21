<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Listing;
use App\User;
use Carbon\Carbon;
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

    $date = Carbon::today();

    $days = [
        $date->addDays(1)->format("d-m-Y") => true,
        $date->addDays(1)->format("d-m-Y") => true,
        $date->addDays(1)->format("d-m-Y") => true,
        $date->addDays(1)->format("d-m-Y") => true,
        $date->addDays(1)->format("d-m-Y") => true,
        $date->addDays(1)->format("d-m-Y") => true,
        $date->addDays(1)->format("d-m-Y") => true,

    ];

    return [
        'user_id' => factory(User::class)->create()->id,
        'item' => json_encode($item),
        'location' => json_encode($location),
        'days' => json_encode($days),
        'price' => $faker->randomFloat(2,1,1000),
    ];

});
