<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', function () {
    return view('welcome');
});

///// Verify User /////
Route::group(
    [
        'namespace' => 'App\Http\Controllers',
    ],
    function ($router) {
        Route::get(
            '/verify-user/{user}/{token}',
            'VerificationController@verify_user'
        );

        Route::get(
            '/verify-payment/{paymentRef}',
            'VerificationController@verify_payment'
        );
    }
);
