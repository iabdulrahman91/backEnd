<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    //
    protected $attributes = [
        'active' => true
    ];

    protected $fillable = [
        'item','location','price', 'days',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

}
