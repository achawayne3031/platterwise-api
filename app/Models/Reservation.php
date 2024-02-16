<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\AppUser;

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
        'guests',
        'all_guests',
        'cancel_reason',
        'code',
        'is_splitted',
    ];

    public function owner()
    {
        return $this->hasOne(AppUser::class, 'id', 'uid');
    }

    public function restaurant()
    {
        return $this->hasOne(Resturant::class, 'id', 'restaurant_id');
    }

    public function reservation_bill()
    {
        return $this->hasOne(ReservationBills::class, 'reservation_id');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 0);
    }

    public function scopePending($query)
    {
        return $query->where('status', 1);
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 2);
    }

    public function scopeInprogress($query)
    {
        return $query->where('status', 3);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 4);
    }

    public function scopeJan($query)
    {
        return $query->whereMonth('created_at', '01');
    }

    public function scopeFeb($query)
    {
        return $query->whereMonth('created_at', '02');
    }

    public function scopeMar($query)
    {
        return $query->whereMonth('created_at', '03');
    }

    public function scopeApril($query)
    {
        return $query->whereMonth('created_at', '04');
    }

    public function scopeMay($query)
    {
        return $query->whereMonth('created_at', '05');
    }

    public function scopeJune($query)
    {
        return $query->whereMonth('created_at', '06');
    }

    public function scopeJuly($query)
    {
        return $query->whereMonth('created_at', '07');
    }

    public function scopeAug($query)
    {
        return $query->whereMonth('created_at', '08');
    }

    public function scopeSept($query)
    {
        return $query->whereMonth('created_at', '09');
    }

    public function scopeOct($query)
    {
        return $query->whereMonth('created_at', '10');
    }

    public function scopeNov($query)
    {
        return $query->whereMonth('created_at', '11');
    }

    public function scopeDec($query)
    {
        return $query->whereMonth('created_at', '12');
    }
}
