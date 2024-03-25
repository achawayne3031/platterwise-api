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

    public function top_performing()
    {
        $restaurant = DBHelpers::all_data(Resturant::class);
        $res_data = [];
        $top_performer = [];
        $response_data = [];
        $top_sales = [];

        if (count($restaurant) > 0) {
            foreach ($restaurant as $value) {
                ///// Top performer /////
                $data = [
                    'total_reservation' => DBHelpers::count(
                        Reservation::class,
                        [
                            'restaurant_id' => $value->id,
                        ]
                    ),
                    'name' => $value->name,
                    'email' => $value->email,
                    'phone' => $value->phone,
                    'address' => $value->address,
                ];

                array_push($res_data, $data);

                ///// top sales /////
                Transactions::where(['restaurant_id' => $value->id])->sum(
                    'amount_paid'
                );

                $sales_data = [
                    'total_sales' => Transactions::where([
                        'restaurant_id' => $value->id,
                    ])->sum('amount_paid'),
                    'name' => $value->name,
                    'email' => $value->email,
                    'phone' => $value->phone,
                    'address' => $value->address,
                ];

                array_push($top_sales, $sales_data);
            }

            /////// Sorting Top performer //////////
            usort($res_data, function ($a, $b) {
                //Sort the array using a user defined function
                return $a['total_reservation'] > $b['total_reservation']
                    ? -1
                    : 1; //Compare the scores
            });

            /////// Sorting Top Sales //////////
            if (count($top_sales) > 0) {
                usort($top_sales, function ($a, $b) {
                    //Sort the array using a user defined function
                    return $a['total_sales'] > $b['total_sales'] ? -1 : 1; //Compare the scores
                });
            }

            $arrange = [
                'top_performer' => count($res_data) > 0 ? $res_data[0] : [],
                'top_sales' => count($top_sales) > 0 ? $top_sales[0] : [],
            ];
        }

        return ResponseHelper::success_response(
            'Top performing restuarant',
            $arrange
        );
    }

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

            $value->total_amount_paid = Transactions::where([
                'restaurant_id' => $value->id,
            ])->sum('amount_paid');
        }

        return ResponseHelper::success_response(
            'All restaurant analysis data fetched was successfully',
            $recent_restaurant
        );
    }

    public function index(Request $request)
    {
        $recent_restaurant = DBHelpers::data_desc_take(Resturant::class, [], 8);

        foreach ($recent_restaurant as $value) {
            $value->total_reservation = DBHelpers::count(Reservation::class, [
                'restaurant_id' => $value->id,
            ]);
            $value->total_cancelled_reservation = DBHelpers::count(
                Reservation::class,
                ['restaurant_id' => $value->id, 'status' => 0]
            );

            $value->total_amount_paid = Transactions::where([
                'restaurant_id' => $value->id,
            ])->sum('amount_paid');
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
