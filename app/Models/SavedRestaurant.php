<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedRestaurant extends Model
{
    use HasFactory;

    protected $table = 'saved_restaurant';

    protected $fillable = ['restaurant_id', 'uid'];

    public function restaurant()
    {
        return $this->hasMany(Resturant::class, 'id', 'restaurant_id');
    }
}
