<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationSplitBills extends Model
{
    use HasFactory;

    protected $table = 'reservation_split_bills';

    protected $fillable = ['reservation_id', 'total_amount', 'guests'];

    public function owner()
    {
        return $this->hasOne(AppUser::class, 'id', 'uid');
    }

    public function reservation()
    {
        return $this->hasOne(Reservation::class, 'id', 'reservation_id');
    }
}
