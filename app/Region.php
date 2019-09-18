<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    //
    protected $fillable = [
        'id', 'capital_city_id', 'code', 'name_ar', 'name_en', 'population', 'center','boundaries',
    ];

    function cities(){
        return $this->hasMany('App\City');
    }

    function capital(){
        return $this->hasOne('App\City', 'id', 'capital_city_id');
    }

    function districts(){
        return $this->hasMany('App\District');
    }
}
