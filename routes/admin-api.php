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
        'namespace' => 'App\Http\Controllers\SuperAdmin\Auth',
    ],
    function ($router) {
        Route::post('/login', 'AuthController@login');
        Route::post('/register', 'AuthController@register');
    }
);

///// Dashboard /////
Route::group(
    [
        'middleware' => ['cors', 'super.admin'],
        'prefix' => 'dashboard',
        'namespace' => 'App\Http\Controllers\SuperAdmin',
    ],
    function ($router) {
        Route::get('/index', 'DashboardController@dashboard');
    }
);

///// Restuarant /////
Route::group(
    [
        'middleware' => ['cors', 'super.admin'],
        'prefix' => 'restaurant',
        'namespace' => 'App\Http\Controllers\SuperAdmin',
    ],
    function ($router) {
        Route::get('/index', 'RestaurantController@index');
        Route::get('/all-restaurants', 'RestaurantController@all_restaurants');

        Route::get('/top-performing', 'RestaurantController@top_performing');
    }
);

///// Reservation /////
Route::group(
    [
        'middleware' => ['cors', 'super.admin'],
        'prefix' => 'reservation',
        'namespace' => 'App\Http\Controllers\SuperAdmin',
    ],
    function ($router) {
        Route::get(
            '/all-reservations',
            'ReservationController@all_reservations'
        );
    }
);

///// Dashboard /////
Route::group(
    [
        'middleware' => ['cors', 'super.admin'],
        'prefix' => 'user-list',
        'namespace' => 'App\Http\Controllers\SuperAdmin',
    ],
    function ($router) {
        Route::get('/index', 'UserController@index');

        Route::get('/all', 'UserController@user_list');
        Route::post('/delete-user', 'UserController@delete_user');
        Route::post('/view-user', 'UserController@view_user');
    }
);
