<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empreinte extends Model
{
    use HasFactory;
    protected $fillable = ['demandeur_id', 'empreinte_hash', 'empreinte_data'];

    public function demandeur()
    {
        return $this->belongsTo(Demandeur::class);
    }
}
