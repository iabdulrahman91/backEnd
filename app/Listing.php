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
        'item','location', 'deliverable', 'price', 'days',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function rentRequests(){
        return $this->hasMany(RentRequest::class, 'listing_id');
    }

    public function bookings(){
        return $this->hasMany(Booking::class, 'listing_id');
    }

    public function city(){
        return $this->hasOne(City::class, 'id', 'location');
    }
}
