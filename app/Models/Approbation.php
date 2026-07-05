<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approbation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'saison',
        'date_approbation',
        'reference',
        'date_debut',
        'date_fin',
        'compagnie_id',
        'demande_id'
    ];

    /**
     * Get the compagnie associated with the approbation.
     */
    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }

    /**
     * Get the demande associated with the approbation.
     */
    public function demande()
    {
        return $this->belongsTo(DemandeApprobation::class);
    }

    /**
     * Format the date_approbation attribute.
     *
     * @return string
     */
    public function getFormattedDateApprovalAttribute()
    {
        return \Carbon\Carbon::parse($this->date_approbation)->format('d/m/Y');
    }

    /**
     * Format the date range (debut to fin).
     *
     * @return string
     */
    public function getFormattedDateRangeAttribute()
    {
        $start = \Carbon\Carbon::parse($this->date_debut)->locale('fr')->isoFormat('D MMMM');
        $end = \Carbon\Carbon::parse($this->date_fin)->locale('fr')->isoFormat('D MMMM YYYY');
        return "Du $start au $end";
    }

    /**
     * Get the full reference with season and year.
     *
     * @return string
     */
    public function getFullReferenceAttribute()
    {
        return $this->saison . ' ' . \Carbon\Carbon::parse($this->date_approbation)->format('Y');
    }

    public function getPreviousApproval()
    {
        return static::whereHas('demande', function ($query) {
            $query->where('compagnie_id', $this->demande->compagnie_id);
        })
            ->where('date_approbation', '<', $this->date_approbation)
            ->orderBy('date_approbation', 'desc')
            ->first();
    }
}
