<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Listing;
use App\RentRequest;
use App\Rules\Days;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\RentRequest as RentRequestResource;

class RentRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = Auth::user();
        $received = $user->receivedRentRequests;
        $sent = $user->sentRentRequests;

        return response()
            ->json([
                'sent' => RentRequestResource::collection($sent),
                'received' => RentRequestResource::collection($received),
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\RentRequest $rentRequest
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        // only owner or customer can view it
        $user = Auth::user();

        $rr = RentRequest::find($id);

        if ($user->id != $rr->customer_id && $user->id != $rr->listing->user_id) {
            return response(['error' => 'Unauthorized.'], 401);
        }

        return new RentRequestResource($rr);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $user = Auth::user();


        // see Rules/ for more info about the validation process
        $validator = Validator::make($request->all(), [
            'listing_id' => ['Required'],
            'days' => ['Required', new Days()],
        ]);

        // return bad request code with error msg
        if ($validator->fails()) {
            return response()
                ->json(['error' => $validator->errors()])
                ->setStatusCode(400);
        }

        // to prevent duplicate rent requests
        $rr = RentRequest::where('customer_id', $user->id)
            ->where('listing_id', $request['listing_id'])
            ->whereIn('status', [0,1])
            ->first();

        if ($rr != null) {
            return response()
                ->json(['error' => 'rent request exist'], 400, []);
        }

        // get the requested listing
        // if doesn't exist return bad request error
        $listing = Listing::find($request['listing_id']);
        if ($listing == null) {
            return response(['error' => 'listing does not exist.'], 400);
        }


        if ($listing->user->id == $user->id){
            return response(['error' => 'you can not send rent request to yourself.'], 400);
        }

        // prepare rentRequest data

        // format the dates to day-month-year : 24-12-1991
        $days = array();
        foreach ($request['days'] as $d) {
            array_push($days, Carbon::parse($d)->format('d-m-Y'));
        }

        // ensure requested days are available
        $listingDays = json_decode($listing->days);
        $invalidDays = array();
        foreach ( $days as $d){
            if (!isset($listingDays->{$d}) || $listingDays->$d != 1){
                array_push($invalidDays, $d);
            }
        }

        if (count($invalidDays)){
            return response()
                ->json(['error' => 'unavailable days'], 400, []);
        }
        // instantiate listing object
        $rentRequest = new RentRequest([
            'listing_id' => $listing->id,
            'customer_id' => $user->id,
            'owner_id' => $listing->user_id,
            'days' => json_encode($days),
            'cost' => ($listing->price),

        ]);

        // pass the listing object to be created by the user
        $user->sentRentRequests()->save($rentRequest);


        // confirm success
        return new RentRequestResource($rentRequest);
    }


    public function update(Request $request, int $id){
        $rr = RentRequest::find($id);
        $user = Auth::user();

        // if the rent request is not still open return erro
        if ($rr == null || $rr->status != 0){
            return response(['error' => 'Not Found.'], 404);
        }

        // only customer can update their rent requests
        if ($user->id != $rr->customer_id){
            return response(['error' => 'Unauthorized.'], 401);
        }

        // validate days
        $validator = Validator::make($request->all(), [
            'days' => ['Required', new Days()],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['error' => $validator->errors()])
                ->setStatusCode(400);
        }

        // in case the listing is deleted
        $listing = Listing::find($rr->listing_id);
        if ($listing == null) {
            return response(['error' => 'listing does not exist anymore.'], 400);
        }

        // make sure days are available
        $days = array();
        foreach ($request['days'] as $d) {
            array_push($days, Carbon::parse($d)->format('d-m-Y'));
        }

        // ensure requested days are available
        $listingDays = json_decode($listing->days);
        $invalidDays = array();
        foreach ( $days as $d){
            if (!isset($listingDays->{$d}) || $listingDays->{$d} != 1){
                array_push($invalidDays, $d);
            }
        }

        if (count($invalidDays)){
            return response()
                ->json(['error' => 'unavailable days'], 400, []);
        }

        // update RentRequest
        $rr->days = json_encode($days);
        $rr->save();


        // return updated
        return new RentRequestResource($rr);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\RentRequest $rentRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        //
        // only owner or customer can view it
        $user = Auth::user();

        $rr = RentRequest::find($id);

        // customer cancel
        if ($user->id == $rr->customer_id)  {
            $rr->status = 3;
            $rr->save();

        // owner reject
        } elseif ($user->id == $rr->listing->user_id) {
            $rr->status = 2;
            $rr->save();
        } else {
            return response(['error' => 'Unauthorized.'], 401);
        }

        return new RentRequestResource($rr);
    }
}
