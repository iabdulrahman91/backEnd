<?php

namespace Tests\Feature\Listing;

use App\Listing;
use App\User;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowListingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class ListingTableSeeder');
    }


    public function test_show_listing(){

        $l = Listing::first();
        $res = $this->get('api/listings/'.$l->id)->assertOk();

        $res->assertJsonStructure(['data' => ['id','user_id','location','item','days','price','active']]);
        $this->assertSame($l->id, json_decode($res->getContent())->data->id);
    }



}
