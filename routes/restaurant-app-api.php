<?php

///// Auth /////
Route::group(
    [
        'middleware' => ['cors'],
        'prefix' => 'auth',
        'namespace' => 'App\Http\Controllers\Team\Auth',
    ],
    function ($router) {
        Route::post('/team-login', 'AuthController@team_login');

        Route::get('/test', 'AuthController@test');
    }
);

///// Restaurant Team /////
Route::group(
    [
        'middleware' => ['cors', 'jwt.verify:team-api'],
        'prefix' => 'reservations',
        'namespace' => 'App\Http\Controllers\Team',
    ],
    function ($router) {
        Route::post(
            '/pending-reservations',
            'ReservationController@pending_reservations'
        );

        Route::get(
            '/view-reservation/{id}',
            'ReservationController@view_reservation'
        );

        Route::post('/check-in-reservations', 'ReservationController@check_in');

        ////   Route::post('/team-login', 'AuthController@team_login');
    }
);
