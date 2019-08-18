<?php

namespace App\Http\Controllers\API;
use Exception;
use App\Listing;
use App\Rules\Days;
use App\Rules\Item;
use App\Rules\Location;
use Carbon\Carbon;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\http\resources\Listing as ListingResource;

class ListingController extends Controller
{

    /**
     * Guest can access:
     *  - index
     *  - show?
     *
     * Auth can access all.
     */


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $listings = Listing::where('active',true)->paginate(10);
        return ListingResource::collection($listings);
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Listing $listing
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
        $listing = Listing::find($id);

        // make user the requester it the owner
        if ($listing == null) {
            return response()
                ->json(['message' => 'Not Found.'])
                ->setStatusCode(404);
        }
        return new ListingResource($listing);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // get data from the request

        $user = Auth::user();



        // see Rules/ for more info about the validation process
        $validator = Validator::make($request->all(), [
            'location' => ['Required', new Location()],
            'item' => ['Required', new Item()],
            'days' => ['Required', new Days()],
            'price' => ['Required', 'Numeric'],
        ]);

        // return bad request code with error msg
        if ($validator->fails()) {
            return response()
                ->json(['error' => $validator->errors()])
                ->setStatusCode(400);
        }


        // prepare listing data
        $location = json_encode($request['location']);
        $item = json_encode($request['item']);
        $price = ($request['price']);


        // format the dates to day-month-year : 24-12-1991
        $days = array();
        foreach ($request['days'] as $d) {
            $days[Carbon::parse($d)->format('d-m-Y')] = 1;
        }
        $days = json_encode($days);

        // instantiate listing object
        $listing = new Listing([
            'location' => $location,
            'item' => $item,
            'price' => $price,
            'days' => $days,

        ]);

        // pass the listing object to be created by the user
        $user->listings()->save($listing);


        // confirm success
        return new ListingResource($listing);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Listing $listing
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        // verify user's authority, and listing existence
        $user = Auth::user();
        $listing = Listing::find($id);

        // make user the requester it the owner
        if ($listing == null || $listing->user->id != $user->id) {
            return response()
                ->json(['message' => 'Unauthorized.'])
                ->setStatusCode(401);
        }

        // validate new data
        $validator = Validator::make($request->all(), [
            'days' => ['sometimes', 'Required', new Days()],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['error' => $validator->errors()])
                ->setStatusCode(400);
        }

        // if no updated attributes is submitted
        if (!$request->has(['days']) || empty($request['days'])) {
            return response()
                ->json(['message' => 'No update made']);
        }


        // update the listing
        /*
         * Days status:
         * 0 : inactive / pending
         * 1 : active and can be requested.
         * 2 : rented.
         * 3 : user delete.
         * 4 : management delete.
         */
        $oldDays = json_decode($listing->days);
        $newDays = array();


        // deactivate all active days. Leave other dates if not active (1)
        // only set the value for active days (1) or whatever default value for new listing days
        // default value now is 1 as the mgmt allow users to directly publish their listings. However, in the future we might have
        // mgmt approving new list. In this case we need to consider pending days candidate for update to 3.
        // make sure no date impact current transaction
        foreach ($oldDays as $d => $s) {
            $newDays[$d] = ($s = 1)? 3 : $s;
        }


        // format the dates to day-month-year : 24-12-1991
        // and add new days
        // just add new dates or update the one deleted by user (3)
        foreach ($request['days'] as $d) {
            $day = Carbon::parse($d)->format('d-m-Y');

            try {
                $newDays[$day] = ($newDays[$day] == 3 )? 1 : $newDays[$day];
            } catch (Exception $error){
                $newDays[$day] = 1;
            }

        }

        // assign new days to the listing
        $listing->days = json_encode($newDays);

        // pass the listing object to be created by the user
        $l = $user->listings()->save($listing);


        // confirm success
        return new ListingResource($l);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Listing $listing
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $user = Auth::user();
        $listing = Listing::find($id);

        // make user the requester it the owner
        if ($listing == null || $listing->user->id != $user->id) {
            return response()
                ->json(['message' => 'Unauthorized.'])
                ->setStatusCode(401);
        }

        // change the listing status to false
        $listing->active = false;

        // pass the listing object to be created by the user
        $l = $user->listings()->save($listing);


        // confirm success
        return new ListingResource($l);


    }
}
