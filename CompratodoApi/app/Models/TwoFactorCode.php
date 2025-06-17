<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwoFactorCode extends Model
{
    protected $fillable = [
        'user_id', 'code', 'method', 'expires_at', 'verified_at'
    ];

    protected $dates = ['expires_at', 'verified_at'];
}

