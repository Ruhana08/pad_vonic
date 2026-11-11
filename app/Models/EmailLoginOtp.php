<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLoginOtp extends Model
{
    protected $table = 'email_login_otps';

    protected $fillable = [
        'email',
        'otp_hash',
        'attempts',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
