<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantSeatType extends Model
{
    use HasFactory;

    protected $table = 'restaurant_seat_type';

    protected $fillable = ['restaurant_id', 'status', 'name'];

    public function restaurant()
    {
        return $this->hasMany(Resturant::class, 'id', 'restaurant_id');
    }
}
