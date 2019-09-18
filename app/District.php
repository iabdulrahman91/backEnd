<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    //
    protected $fillable = [
        'district_id', 'city_id', 'region_id', 'name_ar', 'name_en','boundaries',
    ];

    function region(){
        return $this->belongsTo('App\Region');
    }
    function city(){
        return $this->belongsTo('App\City');
    }
}
