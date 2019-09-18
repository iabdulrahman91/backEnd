<?php

namespace Tests\Feature\Booking;

use App\RentRequest;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateBookingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class RentRequestTableSeeder');

    }

    public function test_update_IS_NOT_ALLOWED()
    {
        $owner = User::first();
        Passport::actingAs($owner);

        $res = $this->json('PUT', 'api/bookings/0',
            [
                'rentRequest_id' => 0
            ],
            ['Accept' => 'application/json', 'Content-type' => 'application/json']);

        $res->assertStatus(405);
        $res->assertJsonStructure(['error']);

    }
}
