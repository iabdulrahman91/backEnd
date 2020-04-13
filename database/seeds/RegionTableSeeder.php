<?php

use Illuminate\Database\Seeder;
use App\Region;

class RegionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('regions')->delete();
        $json = File::get('database/data/regions.json');
        $data = json_decode($json);
        foreach ($data as $obj) {
            Region::create(array(
                'id' => $obj->region_id,
                'capital_city_id' => $obj->capital_city_id,
                'code' => $obj->code,
                'name_ar' => $obj->name_ar,
                'name_en' => $obj->name_en,
                'population' => $obj->population,
                'center' => json_encode($obj->center),
                'boundaries' => json_encode($obj->boundaries),
            ));
        }
    }
}