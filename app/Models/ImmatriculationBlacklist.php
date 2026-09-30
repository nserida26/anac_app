<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Motif de blocage d'immatriculation (préfixe ou immatriculation exacte),
 * empêchant la création d'un aéronef correspondant. Voir Rules\NotBlacklistedImmatriculation.
 */
class ImmatriculationBlacklist extends Model
{
    protected $fillable = ['pattern', 'match_type', 'motif', 'created_by'];

    public function creePar()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Vrai si l'immatriculation correspond à un motif de blocage, insensible à la casse :
     * comparaison stricte pour les motifs "exact", par préfixe pour les motifs "prefix".
     */
    public static function isBlacklisted(string $immatriculation): bool
    {
        $normalized = strtoupper(trim($immatriculation));

        if ($normalized === '') {
            return false;
        }

        return static::query()
            ->get(['pattern', 'match_type'])
            ->contains(function (self $entry) use ($normalized) {
                $pattern = strtoupper($entry->pattern);

                return $entry->match_type === 'exact'
                    ? $normalized === $pattern
                    : Str::startsWith($normalized, $pattern);
            });
    }
}
