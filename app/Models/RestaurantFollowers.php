<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantFollowers extends Model
{
    use HasFactory;

    protected $table = 'restaurant_followers';

    protected $fillable = ['uid', 'restaurant_id'];

    public function follower()
    {
        return $this->hasOne(AppUser::class, 'id', 'uid');
    }

    public function restaurant()
    {
        return $this->hasOne(Resturant::class, 'id', 'restaurant_id');
    }
}
