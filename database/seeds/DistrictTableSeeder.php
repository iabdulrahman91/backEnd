<?php

use Illuminate\Database\Seeder;
use App\District;

class DistrictTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('districts')->delete();
        $json = File::get('database/data/districts.json');
        $data = json_decode($json);
        foreach ($data as $obj) {
            District::create(array(
                'id' => $obj->district_id,
                'city_id' => $obj->city_id,
                'region_id' => $obj->region_id,
                'name_ar' => $obj->name_ar,
                'name_en' => $obj->name_en,
                'boundaries' => json_encode($obj->boundaries),
            ));
        }
    }
}
