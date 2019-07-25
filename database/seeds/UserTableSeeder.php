<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $users = factory(App\User::class, 30)
            ->create()
            ->each(function ($user) {
                foreach (range(1,rand(2,5)) as $i){
                    $user->listings()->save(factory(App\Listing::class)->make());
                }

            });
    }
}
