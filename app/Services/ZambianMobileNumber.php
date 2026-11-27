<?php

namespace App\Services;

use InvalidArgumentException;

class ZambianMobileNumber
{
    /** Normalize supported local or +260 Zambian mobile numbers to local 10-digit format. */
    public static function normalize(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', trim($phone));
        if (str_starts_with($digits, '260')) {
            $digits = '0' . substr($digits, 3);
        }

        // Preserve the existing 07/09 ten-digit validation and add the newly allocated 055/056/057 ranges.
        if (!preg_match('/^(?:0[79]\d{8}|0(?:55|56|57)\d{7})$/', $digits)) {
            throw new InvalidArgumentException('Enter a valid 10-digit Zambian mobile number, e.g. 0961111111 or 0561234567.');
        }

        return $digits;
    }

    /** Infer the Lenco operator from a known Zambian mobile prefix. */
    public static function operator(string $phone): string
    {
        $prefix = substr(self::normalize($phone), 0, 3);
        $operators = [
            'mtn' => ['056', '076', '096'],
            'airtel' => ['057', '077', '097'],
            'zamtel' => ['055', '075', '095'],
        ];

        foreach ($operators as $operator => $prefixes) {
            if (in_array($prefix, $prefixes, true)) {
                return $operator;
            }
        }

        throw new InvalidArgumentException('We could not identify the mobile network for this number. Please check the number or contact support.');
    }
}
