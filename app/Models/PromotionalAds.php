<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionalAds extends Model
{
    use HasFactory;

    protected $table = 'promotional_ads';

    protected $fillable = [
        'title',
        'message',
        'from_duration',
        'to_duration',
        'img_url',
        'total_views'
    ];

}
