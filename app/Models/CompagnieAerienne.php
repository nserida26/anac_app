<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompagnieAerienne extends Model
{
    use HasFactory;

    protected $table = 'compagnie_aeriennes';

    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'adresse',
        'user_id'
    ];
}
