<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $table = 'comments';

    protected $guarded = [
        'updated_at',
        'created_at',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $visible = [
        'author',
        'body',
    ];

    /**
     * 投稿者
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id', 'users');
    }
}
