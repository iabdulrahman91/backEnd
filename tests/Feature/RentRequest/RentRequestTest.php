<?php

namespace Tests\Feature\RentRequest;

use App\User;
use Carbon\Carbon;
use function Couchbase\fastlzCompress;
use function GuzzleHttp\describe_type;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RentRequestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp(); //
        $this->artisan('db:seed');
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
        $res->assertJsonCount(1, 'received');


    }

    /*
     * Tests for the show
     */

    public function test_customer_can_view_a_rentRequest()
    {
        // Given authorized user
        $customer = factory(User::class)->create();
        Passport::actingAs($customer);

        $owner = User::first();
        $listing = $owner->listings()->first();


        $today = Carbon::today();

        // and valid new rentRequest
        $res = $this->json('POST', 'api/rentRequests', [
            'listing_id' => $listing->id,
            'days' => [$today->addDays(1)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        // when customer try to see the made request
        $res = $this->json('GET', 'api/rentRequests/' . json_decode($res->getContent())->data->id,
            [],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $res->assertJsonStructure([
            'data' => [
                'id',
                'customer_id',
                'listing_id',
                'days',
                'cost',
                'status'
            ]
        ]);

    }

    public function test_owner_can_view_a_rentRequest()
    {
        // Given authorized user
        $customer = factory(User::class)->create();
        Passport::actingAs($customer);

        $owner = User::first();
        $listing = $owner->listings()->first();


        $today = Carbon::today();

        // and valid new rentRequest
        $res = $this->json('POST', 'api/rentRequests', [
            'listing_id' => $listing->id,
            'days' => [$today->addDays(1)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        // when owner try to see the made request
        Passport::actingAs($owner);
        $res = $this->json('GET', 'api/rentRequests/' . json_decode($res->getContent())->data->id,
            [],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $res->assertJsonStructure([
            'data' => [
                'id',
                'customer_id',
                'listing_id',
                'days',
                'cost',
                'status'
            ]
        ]);

    }

    public function test_other_users_can_Not_view_a_rentRequest_that_does_Not_belong_to_them()
    {
        // Given authorized user
        $customer = factory(User::class)->create();
        Passport::actingAs($customer);

        $owner = User::first();
        $listing = $owner->listings()->first();


        $today = Carbon::today();

        // and valid new rentRequest
        $res = $this->json('POST', 'api/rentRequests', [
            'listing_id' => $listing->id,
            'days' => [$today->addDays(1)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        // when owner try to see the made request
        Passport::actingAs(factory(User::class)->create());
        $res = $this->json('GET', 'api/rentRequests/' . json_decode($res->getContent())->data->id,
            [],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $res->assertStatus(401);

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
            'days' => [$today->addDays(1)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]
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
            'days' => [$today->addDays(1)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]
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
            'days' => [$today->addDays(1)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $res = $this->json('POST', 'api/rentRequests', [
            'listing_id' => $listing->id,
            'days' => [$today->addDays(1)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]
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
            'days' => [$today->addDays(9)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        // Then they should get bad request Error
        $res->assertStatus(400);
        $res->assertJsonStructure(['error']);

    }

    /*
     * Tests for update
     */
    public function test_customer_can_update_rentRequest_days(){
        $this->assertNull(null);
    }


    /*
     * Tests for destory
     */
    public function test_customer_can_cancel_rentRequest(){
        // Given authorized user
        $customer = factory(User::class)->create();
        Passport::actingAs($customer);

        $owner = User::first();
        $listing = $owner->listings()->first();


        $today = Carbon::today();

        // When customer delete rentRequest
        $res = $this->json('POST', 'api/rentRequests', [
            'listing_id' => $listing->id,
            'days' => [$today->addDays(1)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $rr = json_decode($res->getContent())->data;
        $res = $this->json('DELETE', 'api/rentRequests/'.$rr->id, [],
            ['Accept' => 'application/json', 'Content-type' => 'application/json',]);


        // then the status show be 0
        $res = $this->json('GET', 'api/rentRequests/'.$rr->id, [],
            ['Accept' => 'application/json', 'Content-type' => 'application/json',]);

        $rr = json_decode($res->getContent())->data;

        $this->assertEquals(0, $rr->status);
        $this->assertEquals($customer->id, $rr->customer_id);
    }

    public function test_owner_can_reject_rentRequest(){
        // Given authorized user
        $customer = factory(User::class)->create();
        Passport::actingAs($customer);

        $owner = User::first();
        $listing = $owner->listings()->first();


        $today = Carbon::today();

        // When owner cancel rentRequest
        $res = $this->json('POST', 'api/rentRequests', [
            'listing_id' => $listing->id,
            'days' => [$today->addDays(1)->format('d-m-Y'), $today->addDays(1)->format('d-m-Y')]
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $rr = json_decode($res->getContent())->data;
        Passport::actingAs($owner);
        $res = $this->json('DELETE', 'api/rentRequests/'.$rr->id, [],
            ['Accept' => 'application/json', 'Content-type' => 'application/json',]);


        // then the status show be 2
        Passport::actingAs($customer);
        $res = $this->json('GET', 'api/rentRequests/'.$rr->id, [],
            ['Accept' => 'application/json', 'Content-type' => 'application/json',]);

        $rr = json_decode($res->getContent())->data;

        $this->assertEquals(2, $rr->status);
        $this->assertEquals($customer->id, $rr->customer_id);
    }
}
