<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User\AppUser;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class Transactions extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'restaurant_id',
        'reservation_id',
        'user',
        'email',
        'guest_name',
        'payment_type',
        'description',
        'ref',
        'payment_ref',
        'status',
        'extra',
        'init_extra',
        'payment_extra',
        'amount',
        'webhook_extra',
        'amount_paid',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Resturant::class, 'restaurant_id');
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(AppUser::class, 'user');
    }

    function scopefirstWeek($query)
    {
        $date = Carbon::parse('this sunday')->toDateString();
        return $query->where('created_at', '<', $date);
    }

    public function scopeSunday($query)
    {
        return $query->where(
            'created_at',
            CarbonPeriod::between(
                now()->startOfMonth(),
                now()->endOfMonth()
            )->filter(fn($date) => $date->isSunday())
        );
    }

    public function scopeMonday($query)
    {
        return $query->where(
            'created_at',
            CarbonPeriod::between(
                now()->startOfMonth(),
                now()->endOfMonth()
            )->filter(fn($date) => $date->isMonday())
        );
    }

    public function scopeTuesday($query)
    {
        return $query->where(
            'created_at',
            CarbonPeriod::between(
                now()->startOfMonth(),
                now()->endOfMonth()
            )->filter(fn($date) => $date->isTuesday())
        );
    }

    public function scopeWednesday($query)
    {
        return $query->where(
            'created_at',
            CarbonPeriod::between(
                now()->startOfMonth(),
                now()->endOfMonth()
            )->filter(fn($date) => $date->isWednesday())
        );
    }

    public function scopeThursday($query)
    {
        return $query->where(
            'created_at',
            CarbonPeriod::between(
                now()->startOfMonth(),
                now()->endOfMonth()
            )->filter(fn($date) => $date->isThursday())
        );
    }

    public function scopeFriday($query)
    {
        return $query->where('created_at', Carbon::now()->addWeekdays(5));
    }

    public function scopeSaturday($query)
    {
        return $query->where('created_at', Carbon::now()->addWeekdays(6));
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
