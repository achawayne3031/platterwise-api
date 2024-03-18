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

class DashboardController extends Controller
{
    //

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

        // foreach ($users_analytics as $key => $value) {
        //     $key = Carbon::parse($key)->someMethodName();
        // }

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
