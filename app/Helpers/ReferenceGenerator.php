<?php
// app/Helpers/ReferenceGenerator.php
namespace App\Helpers;

class ReferenceGenerator
{
    public static function generateApprovalReference(string $airlineCode, string $seasonYear): string
    {
        // Get the last sequence number used for this year
        $lastRequest = \App\Models\DemandeApprobation::where('reference', 'like', $airlineCode . '-%-' . $seasonYear)
            ->orderBy('id', 'desc')
            ->first();

        // Determine next sequence number (3 digits with leading zeros)
        $sequence = $lastRequest
            ? (int)explode('-', $lastRequest->reference)[1] + 1
            : 1;

        return sprintf(
            '%s-%03d-%s',
            strtoupper($airlineCode),
            $sequence,
            $seasonYear
        );
    }
}
