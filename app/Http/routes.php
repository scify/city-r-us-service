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

Route::group(['middleware' => 'cors', 'prefix' => 'api/v1'], function() {
    Route::post('authenticate', 'Auth\AuthenticateController@authenticate');

    //User routes
    Route::post('users/register', 'UserController@register');
    Route::post('users/authenticate', 'UserController@authenticate');
    Route::post('users/resetPassword', 'UserController@resetPassword');
    Route::post('users/changePassword', 'UserController@changePassword');
    Route::get('users/byEmail', 'UserController@byEmail');
    Route::get('users/byId', 'UserController@byId');
    Route::get('users/byJWT', 'UserController@byJWT');
    Route::get('users', 'UserController@index');
    Route::get('users/withScores', 'UserController@scores');
    Route::post('users/invite', 'InviteController@invite');
    Route::get('users/invite/clicked', 'InviteController@inviteClicked');

    //Mission routes
    Route::get('missions', 'MissionController@index');
    Route::get('missions/observations', 'MissionController@withObservations');
    Route::get('missions/{id}/observations', 'MissionController@byIdWithObservations');
    Route::get('missions/byId', 'MissionController@byId');
    Route::get('missions/byName', 'MissionController@byName');
    Route::post('missions/store', 'MissionController@store');
    Route::post('missions/{id}/update', 'MissionController@update');
    Route::post('missions/update', 'MissionController@update');
    Route::post('missions/{id}/delete', 'MissionController@destroy');
    Route::post('missions/delete', 'MissionController@destroy');
    Route::post('missions/awardUser', 'MissionController@awardUser');
    Route::post('missions/suggest', 'MissionController@suggestMission');
    Route::post('missions/suggestWeb', 'MissionController@suggestMissionWeb');
    Route::get('missions/topContributors', 'MissionController@topContributors');

    //Device routes
    Route::post('devices/register', 'DeviceController@register');

    //Observation routes
    Route::get('observations', 'ObservationController@index');
    Route::post('observations/store', 'ObservationController@store');
    Route::get('getObservationsByMissionId/{missionId}', 'RadicalIntegrationController@getObservations');
    Route::get('getObservationsByMissionId/{missionId}/{date}', 'RadicalIntegrationController@getObservationsByDate');
    Route::post('observations/classify', 'ClassificationController@classify');

    //Map routes
    Route::get('map/venues', 'MapController@getVenues');

    //TEST
    Route::get('test', 'TestController@test');
    //  Points of Interst
    Route::get('map/venues', 'MapController@getVenues');
    //  Events
    Route::get('map/events', 'MapController@getEvents');
});

Route::get('test', 'TestController@test');