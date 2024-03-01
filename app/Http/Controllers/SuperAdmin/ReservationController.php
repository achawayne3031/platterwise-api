<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\DBHelpers;
use App\Helpers\ResponseHelper;
use App\Models\Resturant;
use App\Models\RestaurantSeatType;
use App\Models\RestaurantImages;
use App\Models\RestaurantReviews;
use App\Models\Reservation;
use App\Models\Transactions;
use App\Models\User\AppUser;

class ReservationController extends Controller
{
    //

    public function all_reservations(Request $request)
    {
        $all_reservation = DBHelpers::data_with_paginate(
            Reservation::class,
            ['restaurant'],
            30
        );

        return ResponseHelper::success_response(
            'All reservations analysis data fetched was successfully',
            $all_reservation
        );
    }
}
