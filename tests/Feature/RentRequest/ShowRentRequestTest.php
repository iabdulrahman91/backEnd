<?php

namespace Tests\Feature\RentRequest;

use App\User;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowRentRequestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class RentRequestSeeder');
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


}
