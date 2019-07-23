<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('login', 'Api\UserController@login');
Route::post('register', 'Api\UserController@register');
Route::get('details', 'Api\UserController@details')->middleware('auth:api');


// Listing Guest
Route::get('listings', 'Api\ListingController@index');
Route::get('listings/{listing}', 'Api\ListingController@show');

// Listing Auth
Route::group(['middleware' => 'auth:api'], function () {

    // adding new Listing
    Route::post('listings', 'Api\ListingController@store');

});



