<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetenceDemandeur extends Model
{
    use HasFactory;

    public const TYPE_LINGUISTIQUE = 'Contrôle de compétence linguistique';

    /** Le niveau 6 (expert) n'expire jamais. */
    public const NIVEAU_SANS_EXPIRATION = 6;

    protected $fillable = [
        'type',
        'date',
        'validite',
        'centre_formation_id',
        'niveau',
        'demande_id',
        'document'
    ];

    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }

    /** Date de fin de validité (date + validité en mois), null si elle n'expire pas. */
    public function getDateExpirationAttribute(): ?Carbon
    {
        if ((int) $this->niveau === self::NIVEAU_SANS_EXPIRATION || empty($this->date)) {
            return null;
        }

        return Carbon::parse($this->date)->addMonths((int) $this->validite);
    }
}
