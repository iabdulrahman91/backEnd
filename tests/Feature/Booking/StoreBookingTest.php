<?php

namespace Tests\Feature\Booking;

use App\RentRequest;
use App\User;
use Carbon\Carbon;
use function GuzzleHttp\describe_type;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreBookingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class RentRequestSeeder');

    }

    public function test_user_can_create_Booking_by_approving_RentRequest(){

        $owner = User::first();
        $rr = $owner->receivedRentRequests()->first();

        Passport::actingAs($owner);

        $res = $this->json('POST', 'api/bookings',
            [
                'rentRequest_id' => $rr->id
            ],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);
        $this->assertDatabaseHas('bookings', ['listing_id' => $rr->listing_id, 'customer_id' => $rr->customer_id]);

    }

    public function test_only_owner_can_approve_rentRequest(){

        $owner = User::first();
        $rr = $owner->receivedRentRequests()->first();
        $customer = User::find($rr->customer_id);
        Passport::actingAs($customer);

        $res = $this->json('POST', 'api/bookings',
            [
                'rentRequest_id' => $rr->id
            ],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);
        $res->assertStatus(401);
        $res->assertJsonStructure(['error']);

    }

    public function test_owner_can_NOT_approve_rentRequest_after_user_cancellation(){

        $owner = User::first();
        $rr = $owner->receivedRentRequests()->first();
        Passport::actingAs($owner);
        $rr->status = 3;
        $rr->save();

        $res = $this->json('POST', 'api/bookings',
            [
                'rentRequest_id' => $rr->id
            ],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);
        $res->assertStatus(404);
        $res->assertJsonStructure(['error']);

    }

    public function test_rentRequests_status_got_updated_after_owner_approval(){

        $owner = User::first();
        $rr = $owner->receivedRentRequests()->first();
        Passport::actingAs($owner);

        $res = $this->json('POST', 'api/bookings',
            [
                'rentRequest_id' => $rr->id
            ],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $rr = $owner->receivedRentRequests()->first();
        $this->assertEquals(1, $rr->status);

    }

    public function test_listing_days_status_got_updated_after_owner_approval(){

        $owner = User::first();
        $rr = $owner->receivedRentRequests()->first();
        Passport::actingAs($owner);

        $res = $this->json('POST', 'api/bookings',
            [
                'rentRequest_id' => $rr->id
            ],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $listingDays = json_decode($rr->listing->first()->days);
        $requestedDays = json_decode($rr->days);
        $numOfDays = count($requestedDays);

        $firstDay = $requestedDays[0];
        $lastDay = $requestedDays[$numOfDays-1];

        $afterLastDay = Carbon::createFromFormat('d-m-Y', $lastDay)->addDays(1)->format('d-m-Y');
        $this->assertEquals(false, $listingDays->{$firstDay});
        $this->assertEquals(false, $listingDays->{$lastDay});
        $this->assertEquals(true, $listingDays->{$afterLastDay});

    }

    public function test_other_rentRequests_status_got_updated_after_owner_rejection(){

        $this->withoutExceptionHandling();
        $owner = User::first();
        $rr = $owner->receivedRentRequests()->first();
        Passport::actingAs($owner);
        $rr2 = factory(RentRequest::class)->create(['listing_id' => $rr->listing_id]);
        $rr3 = factory(RentRequest::class)->create(['listing_id' => $rr->listing_id]);
        $res = $this->json('POST', 'api/bookings',
            [
                'rentRequest_id' => $rr->id
            ],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $rr = RentRequest::find($rr->id);
        $rr2 = RentRequest::find($rr2->id);
        $rr3 = RentRequest::find($rr3->id);
        $this->assertEquals(1, $rr->status);
        $this->assertEquals(2, $rr2->status);
        $this->assertEquals(2, $rr3->status);

    }
}
