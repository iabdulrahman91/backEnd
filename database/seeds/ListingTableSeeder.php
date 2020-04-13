<?php

use App\Listing;
use Illuminate\Database\Seeder;

class ListingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('listings')->delete();
        factory(Listing::class, 50)->create();
    }
}
