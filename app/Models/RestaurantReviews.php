<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantReviews extends Model
{
    use HasFactory;

    protected $table = 'restaurant_review';

    protected $fillable = [
        'restaurant_id',
        'comment',
        'star_rating',
        'uid',
        'type',
        'status',
    ];

    public function user()
    {
        return $this->hasMany(AppUser::class, 'id', 'uid');
    }

    public function restaurant()
    {
        return $this->hasMany(Resturant::class, 'id', 'restaurant_id');
    }
}
