<?php
// app/Models/Checklist.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checklist extends Model
{
    protected $fillable = [
        'numero', 
        'index', 
        'type', 
        'libelle', 
        'section', 
        'ordre',
        'type_licence_id',
        'type_demande_id'
    ];

    // Relationship with TypeDemande
    public function typeDemande(): BelongsTo
    {
        return $this->belongsTo(TypeDemande::class, 'type_demande_id');
    }

    // Relationship with TypeLicence (assuming you have this model)
    public function typeLicence(): BelongsTo
    {
        return $this->belongsTo(TypeLicence::class, 'type_licence_id');
    }

    public function demandeChecklists(): HasMany
    {
        return $this->hasMany(ChecklistDemande::class);
    }
}