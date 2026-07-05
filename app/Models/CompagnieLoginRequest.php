<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompagnieLoginRequest extends Model
{
    protected $fillable = [
        'compagnie_user_id',
        'target_user_id',
        'token',
        'expires_at',
        'accepted'
    ];

    public function compagnieUser()
    {
        return $this->belongsTo(User::class, 'compagnie_user_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
