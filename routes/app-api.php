<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

///// Auth /////
Route::group(
    [
        'middleware' => ['cors'],
        'prefix' => 'auth',
        'namespace' => 'App\Http\Controllers\User',
    ],
    function ($router) {
        Route::post('/login', 'AuthController@login');
        Route::post('/token', 'AuthController@token_user');
        Route::post('/set-password', 'AuthController@set_password');
        Route::post('/register', 'AuthController@register');
    }
);

///// Restuarant /////
Route::group(
    [
        'middleware' => ['cors', 'jwt.verify'],
        'prefix' => 'restaurant',
        'namespace' => 'App\Http\Controllers\User',
    ],
    function ($router) {
        Route::get('/index', 'RestaurantController@index');
        Route::post('/near-you', 'RestaurantController@near_you');
        Route::post('/follow', 'RestaurantController@follow');
        Route::post('/unfollow', 'RestaurantController@unfollow');
    }
);

///// Restuarant /////
Route::group(
    [
        'middleware' => ['cors', 'jwt.verify'],
        'prefix' => 'reservation',
        'namespace' => 'App\Http\Controllers\User',
    ],
    function ($router) {
        Route::post('/create', 'ReservationController@create');
        Route::post('/cancel', 'ReservationController@cancel');
    }
);
