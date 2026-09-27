<?php
namespace App\Models;

use App\Models\Concerns\ValidableParAnac;
use Illuminate\Database\Eloquent\Model;

/**
 * Examinateur déclaré par un centre de formation et validé par l'ANAC.
 */
class ExaminateurCentre extends Model
{
    use ValidableParAnac;

    public const COLONNE_CENTRE = 'centre_formation_id';

    protected $table = 'examinateurs_centre';

    protected $fillable = [
        'centre_formation_id',
        'nom',
        'prenom',
        'email',
        'telephone',
        'numero_licence_examinateur',
        'date_naissance',
        'nationalite',
        'adresse',
        'document_justificatif',
        'date_debut_validite',
        'date_fin_validite',
        'statut_validation',
        'motif_refus',
        'valide_par',
        'date_validation'
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_debut_validite' => 'date',
        'date_fin_validite' => 'date',
        'date_validation' => 'datetime'
    ];

    public function centreFormation()
    {
        return $this->belongsTo(CentreFormation::class);
    }

    /** Centre de rattachement (nom commun aux examinateurs déclarés). */
    public function centre()
    {
        return $this->centreFormation();
    }

    public function formations()
    {
        return $this->hasMany(Formation::class, 'examinateur_id');
    }
}
