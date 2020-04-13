<?php

namespace Tests\Feature\Listing;

use App\Listing;
use App\User;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndexListingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class ListingTableSeeder');
    }

    public function test_index_listing(){
        $res = $this->get('api/listings');

        $res->assertJsonStructure(['data','links','meta']);

    }


}
