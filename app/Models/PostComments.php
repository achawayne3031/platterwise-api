<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User\AppUser;

class PostComments extends Model
{
    use HasFactory;

    protected $table = 'post_comments';

    protected $fillable = ['commenter', 'comment', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(AppUser::class, 'commenter');
    }
}
