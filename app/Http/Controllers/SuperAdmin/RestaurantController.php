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

use App\Validations\SuperAdmin\RestaurantValidator;
use App\Validations\ErrorValidation;
use App\Helpers\Func;

class RestaurantController extends Controller
{
    //

    public function all_sales_restaurant(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = RestaurantValidator::validate_rules(
                $request,
                'all_sales'
            );

            if (!$validate->fails() && $validate->validated()) {
                ////   return Carbon::parse('1 April')->month;

                $month_statement =
                    'first day of ' . $request->month . ' ' . date('Y');

                $first_week = Carbon::parse($month_statement)->addWeeks(1);
                $second_week = Carbon::parse($month_statement)->addWeeks(2);
                $third_week = Carbon::parse($month_statement)->addWeeks(3);
                $fourth_week = Carbon::parse($month_statement)->addWeeks(4);

                $first_week_sales = Transactions::where(['status' => 3])
                    ->where('created_at', '>=', $first_week)
                    ->sum('amount_paid');

                $second_week_sales = Transactions::where(['status' => 3])
                    ->whereBetween('created_at', [$first_week, $second_week])
                    ->sum('amount_paid');

                $third_week_sales = Transactions::where(['status' => 3])
                    ->whereBetween('created_at', [$second_week, $third_week])
                    ->sum('amount_paid');

                $fourth_week_sales = Transactions::where(['status' => 3])
                    ->whereBetween('created_at', [$third_week, $fourth_week])
                    ->sum('amount_paid');

                $res_data = [
                    'first_week' => $first_week_sales,
                    'second_week' => $second_week_sales,
                    'third_week' => $third_week_sales,
                    'fourth_week' => $fourth_week_sales,
                ];

                return ResponseHelper::success_response(
                    'Monthly sales',
                    $res_data
                );
            } else {
                $errors = json_decode($validate->errors());
                $props = ['month'];
                $error_res = ErrorValidation::arrange_error($errors, $props);
                return ResponseHelper::error_response(
                    'validation error',
                    $error_res,
                    401
                );
            }
        } else {
            return ResponseHelper::error_response(
                'HTTP Request not allowed',
                '',
                404
            );
        }
    }

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
