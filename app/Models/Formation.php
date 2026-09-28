<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use HasFactory;
    
    protected $table = 'formations';

    public const QUALITES_FORMATEUR = ['instructeur', 'examinateur'];

    /** Le rapport d'examen est confidentiel : disque privé, servi via RapportFormationController. */
    public const DISQUE_RAPPORT = 'prive';

    protected $fillable = [
        'attestation',
        'rapport',
        'demandeur_id',
        'centre_formation_id',
        'type_formation_id',
        'type_licence_id',
        'intitule_formation',
        // Personnel d'un centre de formation (tables instructeurs / examinateurs_centre)
        'instructeur_id',
        'examinateur_id',
        // Détenteur de licence agissant lui-même, en tant qu'instructeur ou examinateur
        'formateur_demandeur_id',
        'qualite_formateur',
        'dispositif_formation_id',
        'lieu',
        'date_formation',
        'status'
    ];
    protected $casts = [
        'date_formation' => 'date',
    ];

    /** Détenteur de licence ayant enregistré la formation (instructeur ou examinateur désigné). */
    public function formateurDemandeur()
    {
        return $this->belongsTo(Demandeur::class, 'formateur_demandeur_id');
    }

    /** Formations enregistrées par ce détenteur de licence. */
    public function scopeDuFormateur($query, Demandeur $formateur)
    {
        return $query->where('formateur_demandeur_id', $formateur->id);
    }

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
