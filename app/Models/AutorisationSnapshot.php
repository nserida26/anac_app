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
            'user.compagnie',
            'avions.compagnie',
            'compagnie',
        ])->first();

        $user      = $demande?->user;
        $demandeur = $user?->demandeur;

        // Opérateur = exploitant du premier aéronef du dossier (distinct du demandeur).
        // À défaut d'aéronef (ex. transport de dépouille mortelle) : l'opérateur
        // explicitement choisi sur la demande, puis en dernier recours une compagnie
        // du compte (arbitraire si le compte en représente plusieurs).
        $premierAvion    = $demande?->avions->first();
        $operateurEntite = optional($premierAvion)->compagnie ?: $demande?->compagnie ?: $user?->compagnie;

        // Le nom est celui figé sur l'aéronef au moment de sa saisie (Avion::booted) :
        // un renommage ultérieur de la compagnie ne doit pas modifier l'historique.
        $operateurNom = optional($premierAvion)->operateur_nom ?: optional($operateurEntite)->nom_entreprise;

        return static::updateOrCreate(
            ['autorisation_id' => $autorisation->id],
            [
                'demandeur_np'          => $demandeur?->np,
                'demandeur_email'       => $user?->email,
                'demandeur_telephone'   => $user?->whatsapp,
                'demandeur_adresse'     => $demandeur?->adresse,
                'demandeur_nationalite' => $demandeur?->nationalite,

                'operateur_nom'         => $operateurNom,
                'operateur_email'       => optional($operateurEntite)->email,
                'operateur_telephone'   => optional($operateurEntite)->telephone,
                'operateur_code'        => optional($operateurEntite)->code,
            ]
        );
    }
}
