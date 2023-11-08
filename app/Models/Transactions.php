<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User\AppUser;

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
        'amount',
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
}
