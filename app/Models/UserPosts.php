<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User\AppUser;

class UserPosts extends Model
{
    use HasFactory;

    protected $table = 'user_posts';

    protected $fillable = [
        'user_id',
        'content_post',
        'content_type',
        'contentUrl',
        'total_likes',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(AppUser::class, 'user_id');
    }
}
