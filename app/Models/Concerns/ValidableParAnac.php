<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Examinateur déclaré par un centre (de formation ou d'expertise médicale)
 * puis validé ou refusé par l'ANAC.
 *
 * Le modèle doit définir la constante COLONNE_CENTRE (clé du centre) et la
 * relation centre().
 */
trait ValidableParAnac
{
    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function getNomCompletAttribute()
    {
        return $this->nom . ' ' . $this->prenom;
    }

    /** Examinateurs déclarés par un centre (tous par défaut). */
    public function scopeDeclares($query)
    {
        return $query;
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut_validation', 'en_attente');
    }

    /** Validés par l'ANAC et dont la validité n'est pas échue. */
    public function scopeValide($query)
    {
        return $query->where('statut_validation', 'valide')
            ->where('date_fin_validite', '>=', now()->toDateString());
    }

    /** Règles du formulaire de déclaration par le centre. */
    public static function reglesDeclaration(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:' . (new static)->getTable() . ',email',
            'telephone' => 'required|string|max:20',
            'numero_licence_examinateur' => 'required|string|max:50',
            'date_naissance' => 'required|date',
            'nationalite' => 'required|string|max:100',
            'adresse' => 'required|string',
            'document_justificatif' => 'required|file|mimes:pdf|max:10240',
            'date_debut_validite' => 'required|date',
            'date_fin_validite' => 'required|date|after:date_debut_validite',
        ];
    }

    /** Déclaration par un centre : l'examinateur attend la validation de l'ANAC. */
    public static function declarer(int $centreId, array $donnees, UploadedFile $document): self
    {
        $examinateur = new static(collect($donnees)->only([
            'nom', 'prenom', 'email', 'telephone', 'numero_licence_examinateur', 'date_naissance',
            'nationalite', 'adresse', 'date_debut_validite', 'date_fin_validite',
        ])->all());
        $examinateur->{static::COLONNE_CENTRE} = $centreId;
        $examinateur->document_justificatif = $document->store('examinateurs/documents', 'public');
        $examinateur->statut_validation = 'en_attente';
        $examinateur->save();

        return $examinateur;
    }

    /** Validation ANAC ; une date de fin vide conserve la date déclarée. */
    public function valider(?string $dateFinValidite = null): void
    {
        $this->statut_validation = 'valide';
        $this->valide_par = Auth::id();
        $this->date_validation = now();
        $this->motif_refus = null;
        if (!empty($dateFinValidite)) {
            $this->date_fin_validite = $dateFinValidite;
        }
        $this->save();

        Log::info('Examinateur validé', ['type' => static::class, 'examinateur_id' => $this->id, 'validated_by' => Auth::id()]);
    }

    public function refuser(string $motif): void
    {
        $this->statut_validation = 'refuse';
        $this->motif_refus = $motif;
        $this->valide_par = Auth::id();
        $this->date_validation = now();
        $this->save();

        Log::info('Examinateur rejeté', ['type' => static::class, 'examinateur_id' => $this->id, 'rejected_by' => Auth::id(), 'motif' => $motif]);
    }
}
