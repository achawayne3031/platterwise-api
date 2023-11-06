<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFollowers extends Model
{
    use HasFactory;

    protected $table = 'user_followers';

    protected $fillable = ['user', 'follower'];

    public function follower()
    {
        return $this->hasOne(AppUser::class, 'id', 'user');
    }
}
