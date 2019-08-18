<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RentRequest extends Model
{
    //
    public $incrementing = true;
    protected $attributes = [
        'status' => 1
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
