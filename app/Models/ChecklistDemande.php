<?php
// app/Models/ChecklistDemande.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistDemande extends Model
{
    protected $table = 'checklist_demande';
    
    protected $fillable = [
        'demande_id', 'checklist_id', 'etat', 'mise_en_oeuvre', 'observations'
    ];

    public function demande(): BelongsTo
    {
        return $this->belongsTo(Demande::class);
    }

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class);
    }
}