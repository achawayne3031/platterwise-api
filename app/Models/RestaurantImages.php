<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantImages extends Model
{
    use HasFactory;

    protected $table = 'restaurant_images';

    protected $fillable = ['restaurant_id', 'image_url', 'status'];

    public function restaurant()
    {
        return $this->hasMany(Resturant::class, 'id', 'restaurant_id');
    }
}
