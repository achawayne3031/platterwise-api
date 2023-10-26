<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Admin\AdminUser;

class Resturant extends Model
{
    use HasFactory;

    protected $table = 'resturant';

    protected $fillable = [
        'admin_uid',
        'name',
        'email',
        'phone',
        'address',
        'state',
        'local_govt',
        'landmark',
        'cover_pic',
        'banner',
        'descriptions',
        'opening_hour',
        'closing_hour',
        'website',
        'social_handle',
        'kyc',
        'latitude',
        'longitude',
        'total_rating',
    ];

    public function owner()
    {
        return $this->hasOne(AdminUser::class, 'id', 'admin_uid');
    }

    public function menu_pic()
    {
        return $this->hasMany(RestaurantImages::class, 'restaurant_id', 'id');
    }

    public function seat_type()
    {
        return $this->hasMany(RestaurantSeatType::class, 'restaurant_id', 'id');
    }

    public function review()
    {
        return $this->hasMany(RestaurantReviews::class, 'restaurant_id', 'id');
    }
}
