<?php

namespace Tests\Feature\RentRequest;

use App\User;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndexRentRequestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class RentRequestSeeder');
    }


    /*
     * Tests for the index
     */
    public function test_user_can_view_rentRequest()
    {
        // Given authorized user
        $user = factory(User::class)->create();
        Passport::actingAs($user);


        // When user hit index rentrequests
        $res = $this->json('GET', 'api/rentRequests', [],
            ['Accept' => 'application/json', 'Content-type' => 'application/json',]);

        // Then the user should see send and received rent requests
        $res->assertOk();
        $res->assertJsonStructure(['sent', 'received']);

    }

    public function test_customer_can_view_SENT_rentRequest()
    {

        // Given authorized user
        $customer = factory(User::class)->create();
        Passport::actingAs($customer);

        $owner = User::first();
        $listing = $owner->listings()->first();


        $today = Carbon::today();

        // When customer make send rentRequest
        $res = $this->json('POST', 'api/rentRequests', [
            'listing_id' => $listing->id,
            'days' => [$today->addDays(1)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        // Then customer can see it using the api
        $res = $this->json('GET', 'api/rentRequests', [],
            ['Accept' => 'application/json', 'Content-type' => 'application/json',]);

        $res->assertJsonCount(1, 'sent');
        $res->assertJsonCount(0, 'received');

    }


    public function test_owner_can_view_RECEIVED_rentRequest()
    {

        // Given authorized user
        $customer = factory(User::class)->create();
        Passport::actingAs($customer);

        $owner = User::first();
        $listing = $owner->listings()->first();


        $today = Carbon::today();

        // When make send rentRequest
        $res = $this->json('POST', 'api/rentRequests', [
            'listing_id' => $listing->id,
            'days' => [$today->addDays(1)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);


        // Then the owner can see it using the api
        Passport::actingAs($owner);
        $res = $this->json('GET', 'api/rentRequests', [],
            ['Accept' => 'application/json', 'Content-type' => 'application/json',]);

        $res->assertJsonCount(0, 'sent');
        $res->assertJsonCount(2, 'received');


    }

}
