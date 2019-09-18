<?php

namespace Tests\Feature\RentRequest;

use App\User;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DestroyRentRequestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class RentRequestTableSeeder');
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

        $this->assertEquals(3, $rr->status);
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
