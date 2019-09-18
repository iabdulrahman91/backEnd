<?php

namespace App\Http\Controllers\API;


use App\City;
use App\Http\Controllers\Controller;
use App\Http\Resources\Cities as CityResource;
use Illuminate\Http\Request;


class CityController extends Controller
{
    public function index(Request $request)
    {
        //
        $cities = City::all();
        return CityResource::collection($cities);
    }

}
