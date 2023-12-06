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
        'middleware' => ['cors', 'jwt.verify', 'user.verified'],
        'prefix' => 'restaurant',
        'namespace' => 'App\Http\Controllers\User',
    ],
    function ($router) {
        Route::get('/index', 'RestaurantController@index');
        Route::post('/near-you', 'RestaurantController@near_you');
        Route::post('/follow', 'RestaurantController@follow');
        Route::post('/unfollow', 'RestaurantController@unfollow');
        Route::post('/save', 'RestaurantController@save');
        Route::post('/unsave', 'RestaurantController@unsave');
        Route::post('/rate', 'RestaurantController@rate');
        Route::post('/view', 'RestaurantController@view');
        Route::get('/followed', 'RestaurantController@followed');
        Route::get('/saved', 'RestaurantController@saved');
        Route::get('/top-rated', 'RestaurantController@top_rated');
        Route::post('/state-filter', 'RestaurantController@state_filter');
        Route::post('/search-filter', 'RestaurantController@search_by_name');
        Route::get('/banner', 'RestaurantController@banner');
    }
);

///// Reservation /////
Route::group(
    [
        'middleware' => ['cors', 'jwt.verify', 'user.verified'],
        'prefix' => 'reservation',
        'namespace' => 'App\Http\Controllers\User',
    ],
    function ($router) {
        Route::post('/create', 'ReservationController@create');
        Route::post('/cancel', 'ReservationController@cancel');
        Route::get('/all', 'ReservationController@all');
        Route::get('/view/{id}', 'ReservationController@view');
        Route::post('/split-bills', 'ReservationController@split_bills');

        Route::post(
            '/get-split-bills',
            'ReservationController@get_split_bills'
        );

        Route::post(
            '/get-reservation-bills',
            'ReservationController@get_reservation_bills'
        );
    }
);

///// Transactions /////
Route::group(
    [
        'middleware' => ['cors', 'jwt.verify'],
        'prefix' => 'transactions',
        'namespace' => 'App\Http\Controllers\User',
    ],
    function ($router) {
        Route::post('/reservation', 'TransactionController@reservation');
    }
);

///// User /////
Route::group(
    [
        'middleware' => ['cors', 'jwt.verify', 'user.verified'],
        'prefix' => 'user',
        'namespace' => 'App\Http\Controllers\User',
    ],
    function ($router) {
        Route::get('/profile', 'UserController@profile');
        Route::post('/edit', 'UserController@edit');
        Route::post('/search-name', 'UserController@search_by_name');

        Route::post('/follow', 'UserController@follow');
        Route::post('/unfollow', 'UserController@unfollow');

        Route::post('/other-user', 'UserController@other_user');
        Route::post('/other-user-posts', 'UserController@other_user_posts');

        Route::post(
            '/other-user-liked-posts',
            'UserController@other_user_liked_posts'
        );

        Route::post('/user-followers', 'UserController@user_followers');
        Route::post('/user-following', 'UserController@user_following');
    }
);

///// Post /////
Route::group(
    [
        'middleware' => ['cors', 'jwt.verify', 'user.verified'],
        'prefix' => 'post',
        'namespace' => 'App\Http\Controllers\User',
    ],
    function ($router) {
        Route::post('/create', 'PostController@create');
        Route::post('/like', 'PostController@like');
        Route::post('/unlike', 'PostController@unlike');
        Route::post('/delete', 'PostController@delete');
        Route::post('/get-post', 'PostController@get_post');
        Route::post('/search-post', 'PostController@search_post');
        Route::get('/all-posts', 'PostController@get_all_posts');
        Route::get('/my-posts', 'PostController@get_my_posts');
        Route::get('/my-liked-posts', 'PostController@get_my_liked_posts');
        Route::get('/top-commented', 'PostController@top_commented');
        Route::get('/top-liked', 'PostController@top_liked');
    }
);

///// Post Comment /////
Route::group(
    [
        'middleware' => ['cors', 'jwt.verify', 'user.verified'],
        'prefix' => 'post-comment',
        'namespace' => 'App\Http\Controllers\User',
    ],
    function ($router) {
        Route::post('/create', 'CommentController@create');
    }
);
