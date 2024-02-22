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

Route::get('/test', function (Request $request) {
    return 'hello world';
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
        'middleware' => ['cors', 'jwt.verify'],
        'prefix' => 'restaurant',
        'namespace' => 'App\Http\Controllers\User',
    ],
    function ($router) {
        Route::get('/index', 'RestaurantController@index');
    }
);

///// Reservation /////
Route::group(
    [
        'middleware' => ['cors', 'jwt.verify'],
        'prefix' => 'reservation',
        'namespace' => 'App\Http\Controllers\User',
    ],
    function ($router) {
        Route::post('/create', 'ReservationController@create');
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
