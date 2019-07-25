<?php

namespace App\Http\Controllers\API;

use App\Listing;
use App\Rules\Days;
use App\Rules\Item;
use App\Rules\Location;
use Carbon\Carbon;
use function GuzzleHttp\describe_type;
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
        $listings = Listing::paginate(10);
        return ListingResource::collection($listings);
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Listing $listing
     * @return \Illuminate\Http\Response
     */
    public function show(Listing $listing)
    {
        //

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
        $inputs = $request->all(['location', 'item', 'days', 'price']);



        // see Rules/ for more info about the validation process
        $validator = Validator::make($inputs, [
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
        $location = json_encode($inputs['location']);
        $item = json_encode($inputs['item']);
        $price = ($inputs['price']);


        // format the dates to day-month-year : 24-12-1991
        $days = array();
        foreach ($inputs['days'] as $d) {
            $days[Carbon::parse($d)->format('d-m-Y')] = true;
        }
        $days = json_encode($days);

        // instantiate listing object
        $listing = new Listing([
            'location' => $location,
            'item' => $item,
            'price' => $price,
            'days' => $days
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
    public function update(Request $request, Listing $listing)
    {
        // TODO
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Listing $listing
     * @return \Illuminate\Http\Response
     */
    public function destroy(Listing $listing)
    {
        // TODO
    }
}
