<?php

namespace App\Services;

use InvalidArgumentException;

class ZambianMobileNumber
{
    /** Normalize Zambian local and +260 mobile numbers to the 10-digit local format. */
    public static function normalize(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', trim($phone));

        if (str_starts_with($digits, '260')) {
            $digits = '0' . substr($digits, 3);
        }

        if (!preg_match('/^0[79]\d{8}$/', $digits)) {
            throw new InvalidArgumentException('Enter a valid Zambian mobile number, e.g. 0961111111 or +260961111111.');
        }

        return $digits;
    }

    /** Infer the Lenco operator from the Zambian mobile prefix. */
    public static function operator(string $phone): string
    {
        $prefix = substr(self::normalize($phone), 0, 3);
        $operators = [
            'mtn' => ['076', '096'],
            'airtel' => ['077', '097'],
            'zamtel' => ['075', '095'],
        ];

        foreach ($operators as $operator => $prefixes) {
            if (in_array($prefix, $prefixes, true)) {
                return $operator;
            }
        }

        throw new InvalidArgumentException('We could not identify the mobile network for this number. Please check the number or contact support.');
    }
}
