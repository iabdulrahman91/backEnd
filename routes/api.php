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

//Location services
Route::get('cities', 'Api\CityController@index');
Route::get('cities/{id}', 'Api\CityController@show');

//Route::group(['middleware' => ['cors']], function () {
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

    // Security: changed {listing}to id because we want to prevent 404 to show.
    // it should send 401 whenever a user try to access other users listings or the listing is not existed
    Route::put('listings/{id}', 'Api\ListingController@update');
    Route::delete('listings/{id}', 'Api\ListingController@destroy');

});

// RentRequest
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('rentRequests', 'API\RentRequestController@index');
    Route::get('rentRequests/{id}', 'API\RentRequestController@show');
    Route::post('rentRequests', 'API\RentRequestController@store');
    Route::put('rentRequests/{id}', 'API\RentRequestController@update');
    Route::delete('rentRequests/{id}', 'API\RentRequestController@destroy');
});


// Booking
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('bookings', 'Api\BookingController@index');
    Route::get('bookings/{id}', 'Api\BookingController@show');
    Route::post('bookings', 'Api\BookingController@store');
    Route::put('bookings/{id}', 'Api\BookingController@update');
    Route::delete('bookings/{id}', 'Api\BookingController@destroy');
});


//});