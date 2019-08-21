<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Listing;
use App\RentRequest;
use App\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(RentRequest::class, function (Faker $faker) {

    $today = Carbon::today();
    $listing = factory(Listing::class)->create();
    return [
        'customer_id' => factory(User::class)->create()->id,
        'listing_id' => $listing->id,
        'days' => json_encode([
            $today->addDays(1)->format('d-m-Y'),
            $today->addDays(1)->format('d-m-Y'),
        ]),
        'cost' => $listing->price * 2,
    ];
});
