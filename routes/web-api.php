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
        'namespace' => 'App\Http\Controllers\Admin\Auth',
    ],
    function ($router) {
        Route::post('/login', 'AuthController@login');
        Route::post('/token', 'AuthController@token_user');
        Route::post('/set-password', 'AuthController@set_password');
        Route::post('/register', 'AuthController@register');
    }
);

///// Restaurant /////
Route::group(
    [
        'middleware' => ['cors', 'jwt.verify'],
        'prefix' => 'restaurant',
        'namespace' => 'App\Http\Controllers\Admin',
    ],
    function ($router) {
        Route::post('/create', 'ResturantController@create');
        Route::get('/all', 'ResturantController@all');
        Route::post('/reviews', 'ResturantController@reviews');
        Route::post('/menu', 'ResturantController@menu');
        Route::post('/delete', 'ResturantController@delete');
        Route::post(
            '/delete-restaurant-picture',
            'ResturantController@delete_restaurant_menu_pic'
        );

        Route::post(
            '/edit-menu-picture',
            'ResturantController@edit_menu_picture'
        );

        Route::post('/view', 'ResturantController@view_restaurant');
    }
);

///// Reservation /////
Route::group(
    [
        'middleware' => ['cors', 'jwt.verify'],
        'prefix' => 'reservation',
        'namespace' => 'App\Http\Controllers\Admin',
    ],
    function ($router) {
        Route::post('/create', 'ResturantController@create');
        Route::post('/all', 'ReservationController@all');
        Route::post('/cancel', 'ReservationController@cancel');
        Route::post('/approve', 'ReservationController@approve');
        Route::post('/edit', 'ReservationController@edit');
        Route::post('/check-in', 'ReservationController@check_in');
    }
);
