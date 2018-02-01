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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function () {
    // Routes for donations api
    Route::Get('donation/user', 'DonationController@getByUser');
    Route::Post('donation', 'DonationController@store');

    // Route for report by user
    Route::Get('report/user', 'ReportController@getByUser');
});



// Routes for reports api
Route::Get('report', 'ReportController@index');
Route::Post('report', 'ReportController@store');

// Route for Currency
Route::Get('currency', 'CurrencyController@index');

Route::post('register', 'Api\RegisterController@register');
Route::post('login', 'Api\LoginController@login');