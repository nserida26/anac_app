<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Copie figée des informations du demandeur et de l'opérateur au moment
 * de la délivrance d'une autorisation. Voir Autorisation::booted().
 */
class AutorisationSnapshot extends Model
{
    protected $table = 'autorisation_snapshots';

    protected $fillable = [
        'autorisation_id',
        'demandeur_np',
        'demandeur_email',
        'demandeur_telephone',
        'demandeur_adresse',
        'demandeur_nationalite',
        'operateur_nom',
        'operateur_email',
        'operateur_telephone',
        'operateur_code',
    ];

    public function autorisation()
    {
        return $this->belongsTo(Autorisation::class, 'autorisation_id');
    }

    /**
     * Construit (ou reconstruit) le snapshot d'une autorisation à partir des
     * données actuellement liées à sa demande.
     */
    public static function buildFor(Autorisation $autorisation): self
    {
        $demande   = $autorisation->demande()->with([
            'user.demandeur',
            'avions.compagnie',
        ])->first();

        $user      = $demande?->user;
        $demandeur = $user?->demandeur;

        // Opérateur = exploitant du premier aéronef du dossier (distinct du demandeur)
        $operateur = optional($demande?->avions->first())->compagnie;

        return static::updateOrCreate(
            ['autorisation_id' => $autorisation->id],
            [
                'demandeur_np'          => $demandeur?->np,
                'demandeur_email'       => $user?->email,
                'demandeur_telephone'   => $user?->whatsapp,
                'demandeur_adresse'     => $demandeur?->adresse,
                'demandeur_nationalite' => $demandeur?->nationalite,

                'operateur_nom'         => $operateur?->nom_entreprise,
                'operateur_email'       => $operateur?->email,
                'operateur_telephone'   => $operateur?->telephone,
                'operateur_code'        => $operateur?->code,
            ]
        );
    }
}
