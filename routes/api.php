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

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
  
    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
        //update user
        Route::post('updateUser', 'AuthController@updateUser');
        Route::post('pushToken', 'AuthController@pushFCMToken');
        // get list of tasks
        Route::get('tasks','TaskController@index');
        Route::get('tasksLatest/{date}','TaskController@indexLatest');
        Route::get('tasksDay','TaskController@tasksDay');
        //get Total Volume per Day
        Route::get('totalVolume','TaskController@totalVolumeperDay');
        // get specific task
        Route::get('task/{id}','TaskController@show');
        // create new task
        Route::post('task','TaskController@store');
        // update existing task
        Route::put('task','TaskController@store');
        // delete a task
        Route::delete('task/{id}','TaskController@destroy');


        // get list of activities
        Route::get('activities','ActivityController@index');
        // get specific activities
        Route::get('activity/{id}','ActivityController@show');
    });
});
// create new activity
Route::post('activity','ActivityController@store');
// update existing activity
Route::put('activity','ActivityController@store');
Route::post('sendNotification','NotificationController@sendNotification');