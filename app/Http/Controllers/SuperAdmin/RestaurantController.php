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
use Carbon\Carbon;

class RestaurantController extends Controller
{
    //

    public function all_restaurants(Request $request)
    {
        $recent_restaurant = DBHelpers::data_paginate(Resturant::class, 30);
        $rest_data = $recent_restaurant->items();

        foreach ($rest_data as $value) {
            $value->total_reservation = DBHelpers::count(Reservation::class, [
                'restaurant_id' => $value->id,
            ]);
            $value->total_cancelled_reservation = DBHelpers::count(
                Reservation::class,
                ['restaurant_id' => $value->id, 'status' => 0]
            );
        }

        return ResponseHelper::success_response(
            'All restaurant analysis data fetched was successfully',
            $recent_restaurant
        );
    }

    public function index(Request $request)
    {
        $recent_restaurant = DBHelpers::data_desc_take(Resturant::class, 8);

        foreach ($recent_restaurant as $value) {
            $value->total_reservation = DBHelpers::count(Reservation::class, [
                'restaurant_id' => $value->id,
            ]);
            $value->total_cancelled_reservation = DBHelpers::count(
                Reservation::class,
                ['restaurant_id' => $value->id, 'status' => 0]
            );
        }

        $first_week = Carbon::parse('first sunday')->toDateString();
        ///  $first_week = Transactions::where('created_at', '<', $date);

        // scopefirstWeek

        $data = [
            'recent_restaurant' => $recent_restaurant,
            'total_reservations' => Reservation::count(),
            'total_cancelled_reservations' => Reservation::rejected()->count(),
            'total_restaurants' => Resturant::count(),
            'first_week' => $first_week,
        ];

        return ResponseHelper::success_response(
            'Restaurant analysis data fetched was successfully',
            $data
        );
    }
}
