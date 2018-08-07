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

/*
 * Users Routes
 */
Route::post('/register','Api\UserController@register');
Route::post('/login','Api\UserController@login');
Route::post('/login-register-with-socail-media','Api\UserController@registerWithSocailMedia');
Route::post('/forgot-password','Api\UserController@forgotPassword');
Route::get('/get-my-profile','Api\UserController@getMyProfile');
Route::post('/update-user-image','Api\UserController@updateUserImage');
Route::post('/update-user-password','Api\UserController@updateUserPassword');
Route::post('/update-user-email','Api\UserController@updateUserEmail');
