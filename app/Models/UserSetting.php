<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasFactory;

    protected $table = 'user_setting';

    /**
     * created_at과 updated_at 컬럼을 사용하지 않는다.
     */
    public $timestamps = false;

    public $fillable = [
        'user_id',
        'is_push',
        'is_push_comment',
        'is_push_reply',
        'is_push_popular',
        'is_sound',
        'is_keep_period',
    ];

    protected $attributes = [
        'is_push' => true,
        'is_push_comment' => true,
        'is_push_reply' => true,
        'is_push_popular' => true,
        'is_sound' => true,
        'is_keep_period' => 0,
    ];
}
