<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $table = 'user_profile';

    protected $fillable = [
        'user_id',
        'profile_id',
        'coin',
        'rice_amount',
        'been_amount',
        'post_count',
        'comment_count',
    ];

    protected $attributes = [
        'profile_id' => 1,
        'coin' => 0,
        'rice_amount' => 0,
        'been_amount' => 0,
        'post_count' => 0,
        'comment_count' => 0,
    ];
}
