<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resturant extends Model
{
    use HasFactory;

    protected $table = 'resturant';

    protected $fillable = [
        'admin_uid',
        'name',
        'email',
        'phone',
        'address',
        'state',
        'local_govt',
        'landmark',
        'cover_pic',
        'banner',
        'descriptions',
        'menu_pic',
        'opening_hour',
        'closing_hour',
        'seat_type',
        'website',
        'social_handle',
        'kyc',
        'latitude',
        'longitude',
    ];

    public function owner()
    {
        return $this->hasOne(AdminUser::class, 'id', 'admin_uid');
    }
}
