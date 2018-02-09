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

use App\Http\Middleware\checkUserId;

Route::get('/', function () {
    return view('welcome');
});

Route::group(
    [
        'middleware' => ['web', 'api'],
        'prefix' => '/api'
    ],
    function() {


});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/test', function(){
   $service = new \App\Http\Service\Treatment();
   $service->gitClone("https://github.com/hackatonITakademy/Hackaton-API.git");
});

Route::get('/mail', function() {
    Mail::send('welcome', ['filename' => 'test.html'], function ($m) {
        $m->from('groupe3@asr.lan', 'Your Application');

//        $m->to($this->data['email'])->subject('Your Reminder!');
        $m->to('groupe3@asr.lan')->subject('Your Reminder!');
        $m->attach(\Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . \App\Http\Service\Treatment::DIR_FILE . '/test.html');
    });
});
