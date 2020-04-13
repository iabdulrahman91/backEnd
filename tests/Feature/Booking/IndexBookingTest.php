<?php

namespace Tests\Feature\Booking;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndexBookingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class RentRequestTableSeeder');



    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_view_all_Booking()
    {

        // Given authorized user
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        // when make get request to Bookings
        $res = $this->json('GET', 'api/bookings',[],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        // Then json response with sent and received booking (reservation and booking)
        $res->assertOk();
        $res->assertJsonStructure(['sent', 'received']);

    }

    public function test_Unauthorized_user_can_NOT_view_bookings()
    {

        // Given authorized user
        $user = factory(User::class)->create();

        // when make get request to Bookings
        $res = $this->json('GET', 'api/bookings',[],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        // Then json response with sent and received booking (reservation and booking)
        $res->assertStatus(401);

    }
}
