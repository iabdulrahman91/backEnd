<?php

namespace App\Http\Controllers\API;

use App\Listing;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // TODO
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function show(Listing $listing)
    {
        //

        return new ListingResource($listing);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Listing $listing)
    {
        // TODO
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function destroy(Listing $listing)
    {
        // TODO
    }
}
