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

        Route::post('/team-logout', 'AuthController@logout');

        Route::get('/test', 'AuthController@test');
    }
);

///// Restaurant Team /////
Route::group(
    [
        'middleware' => ['cors', 'jwt.verify', 'team.user'],
        'prefix' => 'reservations',
        'namespace' => 'App\Http\Controllers\Team',
    ],
    function ($router) {
        Route::post(
            '/pending-reservations',
            'ReservationController@pending_reservations'
        );

        Route::post(
            '/approved-reservations',
            'ReservationController@approved_reservations'
        );

        Route::get(
            '/view-reservation/{id}',
            'ReservationController@view_reservation'
        );

        Route::get(
            '/view-reservation-code/{code}',
            'ReservationController@code'
        );

        Route::post('/check-in-reservations', 'ReservationController@check_in');

        Route::post(
            '/view-reservation-v2',
            'ReservationController@view_reservation_v2'
        );

        Route::post(
            '/view-reservation-v2',
            'ReservationController@view_reservation_v2'
        );

        Route::post(
            '/view-reservation-code-v2',
            'ReservationController@view_reservation_code_v2'
        );

        ////   Route::post('/team-login', 'AuthController@team_login');
    }
);
