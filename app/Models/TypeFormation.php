<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeFormation extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'description','is_instructor'];

    public function formations()
    {
        return $this->hasMany(Formation::class, 'type_formation_id');
    }
}
