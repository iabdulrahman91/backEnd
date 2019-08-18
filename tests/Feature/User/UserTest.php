<?php

namespace Tests\Feature\User;

use App\Listing;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Throwable;

class UserTest extends TestCase
{

    // to use in memory database
    use RefreshDatabase;



    public function setUp(): void
    {
        parent::setUp(); //
        $this->artisan('passport:install');
    }

    /* @test **/
    public function test_user_can_register_using_api()
    {

        // prepare user info
        $userInfo = [
            'fname' => 'test',
            'lname' => 'test',
            'phone' => '0511111111',
            'email' => 'test@test.com',
            'password' => 'testtest',
            'c_password' => 'testtest'
        ];

        // make post request to test the api
        $res = $this->withHeaders(['Accept' => 'application/json', 'Content-type' => 'application/json'])
            ->json('POST','api/register',$userInfo);

        // assert OK
        $res->assertJsonStructure(['user', 'token']);

    }



}
