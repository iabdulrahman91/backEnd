<?php

namespace App\Http\Controllers\API;


use App\City;
use App\Http\Controllers\Controller;
use App\Http\Resources\City as CityResource;
use Illuminate\Http\Request;


class CityController extends Controller
{
    public function index(Request $request)
    {
        //
        $cities = City::all()->keyBy->id;
        return CityResource::collection($cities)->additional(
            ['meta' => [
                'total_cities' => $cities->count(),
            ]
            ]
        );
    }

    public function show(int $id)
    {
        $city = City::findOrFail($id);
        return (new CityResource($city))->additional([
                'listings' => $city->listings,
        ]
        );
    }

}
