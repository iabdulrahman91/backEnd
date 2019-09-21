<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    //
    protected $fillable = [
        'id','region_id', 'name_ar', 'name_en','center',
    ];


    function region(){
        return $this->belongsTo('App\Region');
    }

    function districts(){
       return $this->hasMany('App\District');
    }

    function listings(){
        return $this->hasMany('App\Listing', 'location',null);
    }
}
