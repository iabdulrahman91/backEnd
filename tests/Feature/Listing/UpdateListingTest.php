<?php

namespace Tests\Feature\Listing;

use App\Listing;
use App\User;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateListingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class ListingTableSeeder');
    }


    public function test_user_can_update_listing()
    {

        // given authorized user
        $user = User::first();
        Passport::actingAs($user);

        $listing = $user->listings()->first();
        // when user make post request to add new listing
        $res = $this->json('PUT', 'api/listings/'.$listing->id,
            [

                'days' => [
                    $day1 = Carbon::today()->addDays(10)->format('d-m-Y'),
                    $day2 = Carbon::today()->addDays(11)->format('d-m-Y')
                ]
            ],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);


        // then it should update the day in the user listings()
        $res->assertOk();
        $today = Carbon::today()->addDays()->format('d-m-Y');

        $this->assertEquals(1, json_decode($user->listings()->find($listing->id)->days)->$day1);
        $this->assertEquals(3, json_decode($user->listings()->find($listing->id)->days)->$today);
    }

}
