<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    use HasFactory;

    protected $fillable = ['nom','user_id', 'signature'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
