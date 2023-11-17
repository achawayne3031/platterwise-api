<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\AppUser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LikedPost extends Model
{
    use HasFactory;

    protected $table = 'liked_posts';

    protected $fillable = ['uid', 'post_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(AppUser::class, 'uid');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(UserPosts::class, 'post_id');
    }
}
