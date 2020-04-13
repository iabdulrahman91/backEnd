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

    // hard coded number of cities in Saudi Arabia
    $location = $faker->numberBetween(1, 3946);

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
        'deliverable' => $faker->boolean(),
        'days' => json_encode($days),
        'price' => $faker->randomFloat(2,1,1000),
    ];

});
