<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';

    protected $fillable = [
        'reservation_date',
        'restaurant_id',
        'uid',
        'seat_type',
        'guest_no',
        'subject_of_invite',
    ];

    public function owner()
    {
        return $this->hasOne(AppUser::class, 'id', 'uid');
    }

    public function restaurant()
    {
        return $this->hasOne(Resturant::class, 'id', 'restaurant_id');
    }
}