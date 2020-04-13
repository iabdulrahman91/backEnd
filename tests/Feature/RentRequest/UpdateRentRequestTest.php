<?php

namespace Tests\Feature\RentRequest;

use App\User;
use Carbon\Carbon;
use function GuzzleHttp\describe_type;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateRentRequestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class RentRequestTableSeeder');
    }



    /*
     * Tests for update
     */
    public function test_customer_can_update_rentRequest_days(){
        // Given authorized user
        $customer = factory(User::class)->create();
        Passport::actingAs($customer);

        $owner = User::first();
        $listing = $owner->listings()->first();


        $today = Carbon::today();

        $day1 = $today->addDays(1)->format('d-m-Y');
        $day2 = $today->addDays(1)->format('d-m-Y');
        $day3 = $today->addDays(1)->format('d-m-Y');
        $day4 = $today->addDays(1)->format('d-m-Y');


        // When customer update the sent rentRequest
        $res = $this->json('POST', 'api/rentRequests', [
            'listing_id' => $listing->id,
            'days' => json_encode([$day1, $day2]),
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $rr = $res->json()['data'];


        $res = $this->json('PUT', 'api/rentRequests/'.$rr['id'], [
            'days' => json_encode([$day3, $day4]),
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $res->assertOk();
        // Then see it should have updated days
        $res = $this->json('GET', 'api/rentRequests/' . $rr['id'],
            [],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $updatedDays = json_decode($res->json()['data']['days']);
        $this->assertContains($day3, $updatedDays);
        $this->assertContains($day4, $updatedDays);
        $this->assertNotContains($day1, $updatedDays);
        $this->assertNotContains($day2, $updatedDays);
    }

    public function test_Only_customer_can_update_rentRequest_days(){
        // Given authorized user
        $customer = factory(User::class)->create();
        Passport::actingAs($customer);

        $owner = User::first();
        $listing = $owner->listings()->first();


        $today = Carbon::today();
        $day1 = $today->addDays(1)->format('d-m-Y');
        $day2 = $today->addDays(1)->format('d-m-Y');
        $day3 = $today->addDays(1)->format('d-m-Y');
        $day4 = $today->addDays(1)->format('d-m-Y');



        $res = $this->json('POST', 'api/rentRequests', [
            'listing_id' => $listing->id,
            'days' => json_encode([$day1, $day2]),
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $rr = $res->json()['data'];

        // When other customer update the sent rentRequest
        Passport::actingAs($owner);
        $res = $this->json('PUT', 'api/rentRequests/'.$rr['id'], [
            'days' => [$day3, $day4]
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        // Then user should get unauthorized error msg
        $res->assertStatus(401);
        $res->assertJsonStructure(['error']);


    }

    public function test_Only_valid_rentRequest_can_be_updated(){
        // Given authorized user
        $customer = factory(User::class)->create();
        Passport::actingAs($customer);

        $owner = User::first();
        $listing = $owner->listings()->first();


        $today = Carbon::today();
        $day1 = $today->addDays(1)->format('d-m-Y');
        $day2 = $today->addDays(1)->format('d-m-Y');
        $day3 = $today->addDays(1)->format('d-m-Y');
        $day4 = $today->addDays(1)->format('d-m-Y');



        $res = $this->json('POST', 'api/rentRequests', [
            'listing_id' => $listing->id,
            'days' => json_encode([$day1, $day2]),
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $rr = $res->json()['data'];

        // When other customer update the sent rentRequest
        $res = $this->json('PUT', 'api/rentRequests/33'.$rr['id'], [
            'days' => json_encode([$day3, $day4]),
        ], ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        // Then user should get unauthorized error msg
        $res->assertStatus(404);
        $res->assertJsonStructure(['error']);


    }


}
