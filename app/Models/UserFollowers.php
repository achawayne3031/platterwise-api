<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\AppUser;

class UserFollowers extends Model
{
    use HasFactory;

    protected $table = 'user_followers';

    protected $fillable = ['user', 'follower'];

    public function owner()
    {
        return $this->hasOne(AppUser::class, 'id', 'user');
    }

    public function follower()
    {
        return $this->hasOne(AppUser::class, 'id', 'follower');
    }
}
