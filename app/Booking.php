<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $attributes = [
        'status' => 0
    ];

    protected $fillable = [
        'listing_id','customer_id', 'days', 'cost',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function listing(){
        return $this->belongsTo(Listing::class, 'listing_id');
    }
}
