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



///// Promotional Ads /////
Route::group(
    [
        'middleware' => ['cors', 'super.admin'],
        'prefix' => 'promotional-ads',
        'namespace' => 'App\Http\Controllers\SuperAdmin',
    ],
    function ($router) {
        Route::post(
            '/create',
            'PromtionalAdsController@create'
        );

        Route::get(
            '/get-all-promotional-ads',
            'PromtionalAdsController@get_all_promotional_ads'
        );


        Route::post(
            '/get-promotional-ad',
            'PromtionalAdsController@get_promotional_ad'
        );



        
        
    }
);


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

        Route::post('/validate-email', 'AuthController@validate_email');
        Route::post('/reset-password', 'AuthController@reset_password');
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
        Route::post(
            '/reservtaion-reports',
            'DashboardController@reservation_report'
        );
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

        Route::post(
            '/all-sales-restaurant',
            'RestaurantController@all_sales_restaurant'
        );
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

///// User /////
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

        Route::post('/suspend-user', 'UserController@suspend_user');

        Route::post(
            '/activate-suspended-user',
            'UserController@activate_suspended_user'
        );

        Route::post('/remove-user-post', 'UserController@remove_user_post');

        Route::get(
            '/user-reservation-activities/{user}',
            'UserController@user_reservation_activities'
        );

        Route::get(
            '/user-post-activities/{user}',
            'UserController@user_post_activities'
        );
    }
);
