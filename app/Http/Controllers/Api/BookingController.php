<?php

namespace App\Http\Controllers\API;

use App\Booking;
use App\Http\Controllers\Controller;
use App\RentRequest;
use App\Rules\Days;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Bookings as BookingResource;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $sentBookings = $user->sentBookings;
        $receivedBookings = $user->receivedBookings;

        return response()
            ->json([
                'sent' => BookingResource::collection($sentBookings),
                'received' => BookingResource::collection($receivedBookings),
            ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        // find booking by id
        $booking = Booking::find($id);
        if ($booking == null) {
            return response()
                ->json(['error' => 'Not Found.'])
                ->setStatusCode(404);
        }

        // validate user
        $user = Auth::user();
        if($user->id != $booking->customer_id && $user->id != $booking->listing->user_id){
            return response()
                ->json(['error' => 'Unauthorized.'])
                ->setStatusCode(401);
        }

        return new BookingResource($booking);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate the request
        $validator = Validator::make($request->all(), [
            'rentRequest_id' => ['Required'],
        ]);
        if ($validator->fails()) {
            return response()
                ->json(['error' => $validator->errors()])
                ->setStatusCode(400);
        }


        // find the rent request
        $rr = RentRequest::find($request['rentRequest_id']);
        if ($rr == null || $rr->status != 0) {
            return response()
                ->json(['error' => 'Not Found.'])
                ->setStatusCode(404);
        }


        // only owner can perform this action
        $user = Auth::user();
        if ($user->id != $rr->listing->user_id){
            return response()
                ->json(['error' => 'Unauthorized.'])
                ->setStatusCode(401);
        }


        // update rentRequest status 1 for approval 2 for rejection

        foreach ($rr->listing->rentRequests as $r){
            $r->status = 2;
            $r->save();
        }
        $rr->status = 1;
        $rr->save();

        // update the days of the listing to be unavailable
        $requestedDays = json_decode($rr->days);
        $listing = $rr->listing()->first();
        $listingDays = json_decode($listing->days);
        foreach ($requestedDays as $d){
            $listingDays->{$d} = false;
        }
        $listing->days = json_encode($listingDays);
        $listing->save();

        // make new Booking
        $booking = new Booking([
            'customer_id' => $rr->customer_id,
            'listing_id' => $rr->listing_id,
            'days' => $rr->days,
            'cost' => $rr->cost,
        ]);

        $rr->listing->bookings()->save($booking);

        // return Booking Resource
        return new BookingResource($booking);
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        //
        return response()
            ->json(['error' => 'Method Not Allowed.'])
            ->setStatusCode(405);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy(Booking $booking)
    {
        //
    }
}
