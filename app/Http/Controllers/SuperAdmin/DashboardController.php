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

class DashboardController extends Controller
{
    //

    public function dashboard(Request $request)
    {
        $data = [
            'total_reservations' => Reservation::count(),
            'total_users' => AppUser::count(),
            'total_active_users' => AppUser::active()->count(),
            'total_restaurants' => Resturant::count(),
            'total_income' => Transactions::sum('amount_paid'),
        ];

        return ResponseHelper::success_response(
            'Dashboard analysis data fetched was successfully',
            $data
        );
    }
}
