<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    //
    protected $fillable = [
        'item','location','price', 'days'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }



    public function addDays(array $days){
        $this->days()->saveMany($days);
    }
}
