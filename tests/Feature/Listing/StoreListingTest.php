<?php

namespace Tests\Feature\Listing;

use App\Listing;
use App\User;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreListingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class ListingSeeder');
    }

    public function test_user_can_add_new_listing(){
        // given authorized user
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        // when user make post request to add new listing
        $res = $this->json('POST', 'api/listings',
            [
                'item' => [
                    'company' => 'cannon',
                    'category' => 'body',
                    'product' => 'D3000'],

                'location' => [
                    'lat' => $this->faker->latitude,
                    'lng' => $this->faker->longitude,
                ],

                'price' => $this->faker->randomFloat(2,1,1000),

                'days' => [
                    Carbon::today()->addDays(1)->format('d-m-Y'),
                    Carbon::today()->addDays(2)->format('d-m-Y')
                ]
            ],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $res->assertSuccessful();
        // then it should show in the database, and the user listings()
        $this->assertDatabaseHas('listings', ['user_id' => $user->id]);
        $this->assertEquals('cannon', json_decode($user->listings()->first()->item)->company);
    }



}