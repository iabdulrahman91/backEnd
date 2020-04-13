<?php

use Illuminate\Database\Seeder;
use App\City;

class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('cities')->delete();
        $json = File::get('database/data/cities.json');
        $data = json_decode($json);
        foreach ($data as $obj) {
            City::create(array(
                'id' => $obj->city_id,
                'region_id' => $obj->region_id,
                'name_ar' => $obj->name_ar,
                'name_en' => $obj->name_en,
                'center' => json_encode($obj->center),
            ));
        }
    }
}
