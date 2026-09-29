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
    protected $fillable = ['pattern', 'motif', 'created_by'];

    public function creePar()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Vrai si l'immatriculation correspond à un motif de blocage (préfixe ou exacte),
     * insensible à la casse.
     */
    public static function isBlacklisted(string $immatriculation): bool
    {
        $normalized = strtoupper(trim($immatriculation));

        if ($normalized === '') {
            return false;
        }

        return static::query()
            ->get(['pattern'])
            ->contains(fn (self $entry) => Str::startsWith($normalized, strtoupper($entry->pattern)));
    }
}
