<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User\AppUser;
use App\Models\Admin\AdminUser;
use App\Models\PostComments;

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
        'type',
        'total_comments',
        'admin_uid',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(AppUser::class, 'user_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_uid');
    }

    public function comments()
    {
        return $this->hasMany(PostComments::class, 'post_id');
    }
}
