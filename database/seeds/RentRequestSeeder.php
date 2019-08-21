<?php

use App\RentRequest;
use Illuminate\Database\Seeder;

class RentRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(RentRequest::class, 50)->create();
    }
}
