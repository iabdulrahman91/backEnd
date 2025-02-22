<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fname', 'lname', 'email', 'phone', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    // listings
    public function listings(){
        return $this->hasMany(Listing::class);
    }


    // sent Rent Requests
    public function sentRentRequests(){
        return $this->hasMany(RentRequest::class, 'customer_id');
    }

    // received Rent Requests
    public function receivedRentRequests() {
        return $this->hasManyThrough(RentRequest::class, Listing::class);
    }

    // sent Bookings
    public function sentBookings(){
        return $this->hasMany(Booking::class, 'customer_id');
    }

    // received Bookings
    public function receivedBookings() {
        return $this->hasManyThrough(Booking::class, Listing::class);
    }

}
