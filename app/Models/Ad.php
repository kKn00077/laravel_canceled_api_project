<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $table = 'ad_list';

    protected $fillable = [
        'ad_position',
        'ad_image_url',
        'ad_title',
        'ad_company',
        'ad_note',
        'ad_start_at',
        'ad_end_at',
    ];
}
