<?php

namespace Tests\Feature\Listing;

use App\Listing;
use App\User;
use Carbon\Carbon;
use function GuzzleHttp\describe_type;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp(); //
        $this->artisan('db:seed');
    }

    public function test_index_listing(){
        $res = $this->get('api/listings');

        $res->assertJsonStructure(['data','links','meta']);

    }

    public function test_show_listing(){

        $l = Listing::first();
        $res = $this->get('api/listings/'.$l->id)->assertOk();

        $res->assertJsonStructure(['data' => ['id','user_id','location','item','days','price','active']]);
        $this->assertSame($l->id, json_decode($res->getContent())->data->id);
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
