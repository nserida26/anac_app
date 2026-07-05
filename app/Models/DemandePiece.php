<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandePiece extends Model
{
    use HasFactory;

    protected $table = 'demande_pieces';

    protected $fillable = [
        'titre',
        'url',
        'demande_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }

    // Accessor for full URL
    public function getFullUrlAttribute()
    {
        return $this->url ? asset('/uploads/pieces/' . $this->url) : null;
    }

    // Scope for filtering
    public function scopeByDemande($query, $demandeId)
    {
        return $query->where('demande_id', $demandeId);
    }
}