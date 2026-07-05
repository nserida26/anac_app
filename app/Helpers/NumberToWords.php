<?php

namespace App\Helpers;

class NumberToWords
{
    private static $units = [
        '',
        'un',
        'deux',
        'trois',
        'quatre',
        'cinq',
        'six',
        'sept',
        'huit',
        'neuf',
        'dix',
        'onze',
        'douze',
        'treize',
        'quatorze',
        'quinze',
        'seize',
        'dix-sept',
        'dix-huit',
        'dix-neuf'
    ];

    private static $tens = [
        '',
        '',
        'vingt',
        'trente',
        'quarante',
        'cinquante',
        'soixante',
        'soixante',
        'quatre-vingt',
        'quatre-vingt'
    ];

    public static function convertToWords($number)
    {
        if ($number == 0) {
            return 'zéro';
        }

        $words = '';

        if ($number >= 1000000) {
            $millions = floor($number / 1000000);
            $words .= self::convertToWords($millions) . ' million' . ($millions > 1 ? 's' : '') . ' ';
            $number %= 1000000;
        }

        if ($number >= 1000) {
            $thousands = floor($number / 1000);
            if ($thousands == 1) {
                $words .= 'mille ';
            } else {
                $words .= self::convertToWords($thousands) . ' mille ';
            }
            $number %= 1000;
        }

        if ($number >= 100) {
            $hundreds = floor($number / 100);
            if ($hundreds == 1) {
                $words .= 'cent ';
            } else {
                $words .= self::$units[$hundreds] . ' cent' . ($hundreds > 1 && $number % 100 == 0 ? 's' : '') . ' ';
            }
            $number %= 100;
        }

        if ($number >= 20) {
            $ten = floor($number / 10);
            $words .= self::$tens[$ten];

            if ($ten == 7 || $ten == 9) {
                $unit = $number % 10;
                if ($unit == 1) {
                    $words .= '-et-' . self::$units[$unit + 10];
                } elseif ($unit > 0) {
                    $words .= '-' . self::$units[$unit + 10];
                }
                $number = 0;
            } else {
                $unit = $number % 10;
                if ($unit == 1 && $ten != 8) {
                    $words .= '-et-' . self::$units[$unit];
                } elseif ($unit > 0) {
                    $words .= '-' . self::$units[$unit];
                }
                $number = 0;
            }
            $words .= ' ';
        }

        if ($number > 0) {
            $words .= self::$units[$number] . ' ';
        }

        return trim($words);
    }
}
