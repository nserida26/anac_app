<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use HasFactory;
    
    protected $table = 'formations';

    protected $fillable = [
        'attestation',
        'demandeur_id',
        'centre_formation_id',
        'type_formation_id',
        'type_licence_id',
        'intitule_formation',
        'instructeur_id',
        'examinateur_id',
        'dispositif_formation_id',
        'lieu',
        'date_formation',
        'status'
    ];
    protected $casts = [
        'date_formation' => 'date',
    ];

    /**
     * Relation avec le Demandeur
     */
    public function demandeur()
    {
        return $this->belongsTo(Demandeur::class, 'demandeur_id');
    }

    /**
     * Relation avec le Centre de Formation
     */
    public function centreFormation()
    {
        return $this->belongsTo(CentreFormation::class, 'centre_formation_id');
    }

    /**
     * Relation avec le Type de Formation
     */
    public function typeFormation()
    {
        return $this->belongsTo(TypeFormation::class, 'type_formation_id');
    }

    /**
     * Relation avec le Type de Licence
     */
    public function typeLicence()
    {
        return $this->belongsTo(TypeLicence::class, 'type_licence_id');
    }

    /**
     * Relation avec l'Instructeur
     */
    public function instructeur()
    {
        return $this->belongsTo(Instructeur::class, 'instructeur_id');
    }

    /**
     * Relation avec l'Examinateur
     */
    public function examinateur()
    {
        return $this->belongsTo(ExaminateurCentre::class, 'examinateur_id');
    }

    /**
     * Relation avec le Dispositif de Formation
     */
    public function dispositifFormation()
    {
        return $this->belongsTo(DispositifFormation::class, 'dispositif_formation_id');
    }
}
