<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
