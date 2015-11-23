<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);



Route::group(['middleware' => 'cors', 'prefix' => 'api/v1'], function()
{
    Route::post('authenticate', 'Auth\AuthenticateController@authenticate');


    //User routes
    Route::post('users/register', 'UserController@register');
    Route::post('users/register', 'UserController@register');
    Route::post('users/authenticate', 'UserController@authenticate');
    Route::get('users', 'UserController@index');


    //Mission routes
    Route::get('missions', 'MissionController@index');
    Route::get('missions/byId', 'MissionController@byId');
    Route::get('missions/byName', 'MissionController@byName');
    Route::post('missions/store', 'MissionController@store');
    Route::post('missions/{id}/update', 'MissionController@update');
    Route::post('missions/update', 'MissionController@update');
    Route::post('missions/delete/{id}', 'MissionController@destroy');


    //TEST
    Route::get('test', 'TestController@test');

});


Route::get('test', 'TestController@test');
