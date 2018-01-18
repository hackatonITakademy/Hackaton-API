<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(
    [
        'middleware' => ['web', 'api'],
        'prefix' => '/api'
    ],
    function() {
    Route::Resource('donation', 'DonationController');
    Route::get('donation/user/{user}', 'DonationController@getByUser');
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
