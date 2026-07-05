<?php // app/Enums/SaisonEnum.php
namespace App\Enums;

class SaisonEnum
{
    const ETE = 'ETE';
    const HIVER = 'HIVER';

    public static function getDates(string $saison, int $year): array
    {
        return match ($saison) {
            self::ETE => [
                'date_debut' => "{$year}-03-30",
                'date_fin' => "{$year}-10-25"
            ],
            self::HIVER => [
                'date_debut' => "{$year}-10-26",
                'date_fin' => ($year + 1) . "-03-29"
            ],
            default => throw new \InvalidArgumentException('Invalid season')
        };
    }
}
