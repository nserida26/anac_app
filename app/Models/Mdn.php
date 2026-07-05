<?php
// app/Models/Mdn.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mdn extends Model
{
    use HasFactory;



    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date_autorisation',
        'numero_mdn',
        'pays_id',
        'demande_autorisation_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_autorisation' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the demande autorisation that owns the MDN.
     */
    public function demandeAutorisation(): BelongsTo
    {
        return $this->belongsTo(DemandeAutorisation::class, 'demande_autorisation_id');
    }

    public function pays(): BelongsTo
    {
        return $this->belongsTo(Pays::class, 'pays_id');
    }
    /**
     * Scope a query to filter by pays.
     */
    public function scopeOfPays($query, $pays)
    {
        return $query->where('pays', $pays);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('date_autorisation', [$startDate, $endDate]);
    }

    /**
     * Get formatted date autorisation.
     */
    public function getFormattedDateAutorisationAttribute(): string
    {
        return $this->date_autorisation ? $this->date_autorisation->format('d/m/Y') : '';
    }
    
}