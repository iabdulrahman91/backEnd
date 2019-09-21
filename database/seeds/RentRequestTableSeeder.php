<?php

use App\RentRequest;
use Illuminate\Database\Seeder;

class RentRequestTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('rent_requests')->delete();
        factory(RentRequest::class, 50)->create();
    }
}
