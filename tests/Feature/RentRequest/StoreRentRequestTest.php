<?php

namespace Tests\Feature\RentRequest;

use App\User;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreRentRequestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class RentRequestTableSeeder');
    }



    /*
     * Tests for the store
     */

    public function test_customer_can_SEND_rentRequest()
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
            'days' => json_encode([$today->addDays(1)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]),
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        // Then see it in the database
        $this->assertDatabaseHas('rent_requests', ['customer_id' => $customer->id, 'listing_id' => $listing->id]);
        $this->assertEquals(null, $customer->receivedRentRequests()->first());
        $this->assertEquals($listing->id, $customer->sentRentRequests()->first()->listing_id);

    }

    public function test_owner_can_Receive_rentRequest()
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
            'days' => json_encode([$today->addDays(1)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]),
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        // Then see it in the database
        $this->assertDatabaseHas('rent_requests', ['listing_id' => $listing->id]);
        $this->assertEquals($listing->id, $owner->receivedRentRequests()->first()->listing_id);
        $this->assertEquals(null, $owner->sentRentRequests()->first());

    }

    public function test_customer_can_NOT_SEND_Duplicate_rentRequest()
    {


        $this->withoutExceptionHandling();
        // Given authorized user
        $customer = factory(User::class)->create();
        Passport::actingAs($customer);

        $owner = User::first();
        $listing = $owner->listings()->first();


        $today = Carbon::today();

        // When duplicate send rentRequest
        $res = $this->json('POST', 'api/rentRequests', [
            'listing_id' => $listing->id,
            'days' => json_encode([$today->addDays(1)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]),
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $res = $this->json('POST', 'api/rentRequests', [
            'listing_id' => $listing->id,
            'days' => json_encode([$today->addDays(1)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]),
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        // Then they should get bad request Error
        $res->assertStatus(400);
        $res->assertJsonStructure(['error']);

    }

    public function test_customer_can_NOT_SEND_rentRequest_with_Unavailable_days()
    {

        $this->withoutExceptionHandling();
        // Given authorized user
        $customer = factory(User::class)->create();
        Passport::actingAs($customer);

        $owner = User::first();
        $listing = $owner->listings()->first();


        $today = Carbon::today();

        // When send rentRequest with unavailable days

        $res = $this->json('POST', 'api/rentRequests', [
            'listing_id' => $listing->id,
            'days' => json_encode([$today->addDays(9)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]),
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        // Then they should get bad request Error
        $res->assertStatus(400);
        $res->assertJsonStructure(['error']);

    }
    /*
     * @ignore
     */
    public function test_user_can_NOT_send_rentRequest_to_themselves(){

        // Given authorized user
        $owner = User::first();
        $listing = $owner->listings()->first();


        Passport::actingAs($owner);
        $today = Carbon::today();

        // When user send rentRequest to themselve
        $res = $this->json('POST', 'api/rentRequests', [
            'listing_id' => $listing->id,
            'days' => json_encode([$today->addDays(1)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]),
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);


        // Then they should get bad request Error
        $res->assertStatus(400);
        $res->assertJsonStructure(['error']);
    }

}
