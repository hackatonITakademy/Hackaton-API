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

});

// Routes for donations api
Route::Get('donation', 'DonationController@index');
Route::Get('donation/user/{user}', 'DonationController@getByUser')->middleware(checkUserId::class);
Route::Post('donation', 'DonationController@store')->middleware(checkUserId::class);

// Routes for reports api
Route::Get('report', 'ReportController@index');
Route::Get('report/user/{user}', 'ReportController@getByUser')->middleware(checkUserId::class);
Route::Post('report', 'ReportController@store');

// Route for Currency
Route::Get('currency', 'CurrencyController@index');
Route::Resource('currency', 'CurrencyController');

// Route for Token
Route::get('token', 'TokenController@index');

// Route for User
Route::post('user', 'UserController@store');
Route::post('user/login', 'UserController@login');
Route::post('user/logout', 'UserController@logout');

Route::post('register', 'Api\RegisterController@register');
Route::post('login', 'Api\LoginController@login');