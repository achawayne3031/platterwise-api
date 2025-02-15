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
        Route::post('/validate-email', 'AuthController@validate_email');
        Route::post('/reset-password', 'AuthController@reset_password');
        Route::post('/reset-token', 'AuthController@reset_token');
    }
);

///// Restaurant /////
Route::group(
    [
        'middleware' => ['cors', 'jwt.verify', 'web.user'],
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
        Route::post('/dashboard', 'ResturantController@dashboard');
        Route::post('/edit-restaurant', 'ResturantController@edit_restaurant');
        Route::post('/monthly-income', 'ResturantController@monthly_income');
    }
);

///// Restaurant Team /////
Route::group(
    [
        'middleware' => ['cors', 'jwt.verify', 'web.user'],
        'prefix' => 'restaurant-team',
        'namespace' => 'App\Http\Controllers\Admin',
    ],
    function ($router) {
        Route::post('/register', 'TeamController@register');
        Route::post('/remove-team', 'TeamController@remove_team');
        Route::post('/all-team', 'TeamController@all_team');
    }
);

///// Reservation /////
Route::group(
    [
        'middleware' => ['cors', 'jwt.verify', 'web.user'],
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
        Route::post('/create-bill', 'ReservationController@create_bill');

        Route::post(
            '/view-reservation',
            'ReservationController@view_reservation'
        );

        Route::post(
            '/weekly-reservation-count',
            'ReservationController@weekly_reservation_count'
        );

        Route::post(
            '/monthly-reservation-count',
            'ReservationController@monthly_reservation_count'
        );
    }
);

///// Transactions /////
Route::group(
    [
        'middleware' => ['cors', 'jwt.verify', 'web.user'],
        'prefix' => 'transactions',
        'namespace' => 'App\Http\Controllers\Admin',
    ],
    function ($router) {
        Route::post('/index', 'TransactionController@index');
        Route::post('/reservation', 'TransactionController@reservation');
    }
);

///// Admin Post /////
Route::group(
    [
        'middleware' => ['cors', 'jwt.verify', 'web.user'],
        'prefix' => 'admin-post',
        'namespace' => 'App\Http\Controllers\Admin',
    ],
    function ($router) {
        Route::post('/create', 'AdminPostController@create');
        Route::post('/like', 'PostController@like');
        Route::post('/unlike', 'PostController@unlike');
        Route::post('/delete', 'PostController@delete');
        Route::get('/all-posts', 'PostController@get_all_posts');
        Route::get('/my-posts', 'PostController@get_my_posts');
        Route::get('/my-liked-posts', 'PostController@get_my_liked_posts');
    }
);
