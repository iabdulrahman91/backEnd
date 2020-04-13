<?php

namespace Tests\Feature\Booking;

use App\User;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowBookingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class RentRequestTableSeeder');



    }

    public function test_owner_can_view_a_booking(){
        $owner = User::first();
        $rr = $owner->receivedRentRequests()->first();

        Passport::actingAs($owner);

        $res = $this->json('POST', 'api/bookings',
            [
                'rentRequest_id' => $rr->id
            ],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);
        $booking = json_decode($res->getContent())->data;

        $res = $this->json('GET', 'api/bookings/'. $booking->id,
            [],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $res->assertOk();
        $res->assertJsonStructure(['data']);

    }

    public function test_customer_can_view_a_booking(){
        $owner = User::first();
        $rr = $owner->receivedRentRequests()->first();
        Passport::actingAs($owner);

        $res = $this->json('POST', 'api/bookings',
            [
                'rentRequest_id' => $rr->id
            ],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $booking = json_decode($res->getContent())->data;



        Passport::actingAs(User::find($rr->customer_id));
        $res = $this->json('GET', 'api/bookings/'. $booking->id,
            [],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $res->assertOk();
        $res->assertJsonStructure(['data']);

    }

    public function test_other_user_can_NOT_view_a_booking_that_they_are_NOT_part_from(){
        $owner = User::first();
        $rr = $owner->receivedRentRequests()->first();
        Passport::actingAs($owner);

        $res = $this->json('POST', 'api/bookings',
            [
                'rentRequest_id' => $rr->id
            ],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $booking = json_decode($res->getContent())->data;



        Passport::actingAs(factory(User::class)->create());
        $res = $this->json('GET', 'api/bookings/'. $booking->id,
            [],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $res->assertUnauthorized();

    }
}
