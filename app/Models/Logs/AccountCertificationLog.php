<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountCertificationLog extends Model
{
    use HasFactory;

    protected $table = 'account_certification_logs';

    protected $fillable = [
        'user_id',
        'email',
        'auth_key',
        'is_auth',
        'expiration_at',
    ];
}
