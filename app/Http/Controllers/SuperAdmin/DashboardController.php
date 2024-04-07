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
use Illuminate\Support\Facades\DB;
use App\Validations\SuperAdmin\DashboardValidator;
use App\Validations\ErrorValidation;
use App\Helpers\Func;

class DashboardController extends Controller
{
    //

    public function reservation_report(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = DashboardValidator::validate_rules(
                $request,
                'reservation_report'
            );

            if (!$validate->fails() && $validate->validated()) {
                ////   return Carbon::parse('1 April')->month;

                $month_statement =
                    'first day of ' . $request->month . ' ' . date('Y');

                $first_week = Carbon::parse($month_statement)->addWeeks(1);
                $second_week = Carbon::parse($month_statement)->addWeeks(2);
                $third_week = Carbon::parse($month_statement)->addWeeks(3);
                $fourth_week = Carbon::parse($month_statement)->addWeeks(4);

                $first_week_sales = Reservation::where(
                    'created_at',
                    '>=',
                    $first_week
                )->count();

                $second_week_sales = Reservation::whereBetween('created_at', [
                    $first_week,
                    $second_week,
                ])->count();

                $third_week_sales = Reservation::whereBetween('created_at', [
                    $second_week,
                    $third_week,
                ])->count();

                $fourth_week_sales = Reservation::whereBetween('created_at', [
                    $third_week,
                    $fourth_week,
                ])->count();

                $res_data = [
                    'first_week' => $first_week_sales,
                    'second_week' => $second_week_sales,
                    'third_week' => $third_week_sales,
                    'fourth_week' => $fourth_week_sales,
                ];

                return ResponseHelper::success_response(
                    'Monthly reservation report',
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

    public function dashboard(Request $request)
    {
        $recent_reservation = DBHelpers::with_take_query(Reservation::class, [
            'restaurant',
        ]);

        $users_analytics = DB::table('users')
            ->selectRaw('month(created_at) as month')
            ->selectRaw('count(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        $reservation_analytics = DB::table('reservations')
            ->selectRaw('month(created_at) as month')
            ->selectRaw('count(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        $data = [
            'recent_reservation' => $recent_reservation,
            'total_reservations' => Reservation::count(),
            'total_users' => AppUser::count(),
            'total_active_users' => AppUser::active()->count(),
            'total_restaurants' => Resturant::count(),
            'total_income' => Transactions::sum('amount_paid'),
            'users_analytics' => $users_analytics,
            'reservation_analytics' => $reservation_analytics,
        ];

        return ResponseHelper::success_response(
            'Dashboard analysis data fetched was successfully',
            $data
        );
    }
}
