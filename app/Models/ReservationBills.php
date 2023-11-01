<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationBills extends Model
{
    use HasFactory;

    protected $table = 'reservation_bills';

    protected $fillable = [
        'reservation_id',
        'uid',
        'total_bill',
        'set_picture',
    ];

    public function owner()
    {
        return $this->hasOne(AppUser::class, 'id', 'uid');
    }

    public function reservation()
    {
        return $this->hasOne(Resturant::class, 'id', 'reservation_id');
    }
}
