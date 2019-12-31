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

Route::prefix('auth')->group(function () {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('getUser', 'AuthController@getUser');
        Route::get('logout', 'AuthController@logout');
    });


});


Route::group(['middleware' => 'auth:api'], function () {

    Route::get('tasks', 'TasksController@index');
    Route::get('tasks/{task}', 'TasksController@show');
    Route::post('tasks/store', 'TasksController@store');
    Route::put('tasks/{task}/update', 'TasksController@update');
    Route::delete('tasks/{task}/delete', 'TasksController@destroy');
    Route::patch('tasks/{task}/restore', 'TasksController@restore');

});
