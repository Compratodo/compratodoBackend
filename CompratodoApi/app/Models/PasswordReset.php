<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordReset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'method',
        'expires_at',
        'used_at',
        
    ];

    protected $dates = [
        'expires_at',
        'used_at',
    ];

    /**
     * Relación: este reseteo de contraseña pertenece a un usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
