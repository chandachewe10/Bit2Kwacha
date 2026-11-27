<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class LencoMobileMoneyService
{
    /** Resolve the account holder name for a Zambian mobile-money number. */
    public function resolve(string $phone, ?string $operator = null): array
    {
        $phone = ZambianMobileNumber::normalize($phone);
        $operator = strtolower($operator ?: ZambianMobileNumber::operator($phone));

        if (!in_array($operator, ['mtn', 'airtel', 'zamtel'], true)) {
            throw new \InvalidArgumentException('Choose a supported Zambian mobile-money operator.');
        }

        $token = config('services.lenco.token');
        $baseUri = rtrim((string) config('services.lenco.base_uri'), '/');
        if (!$token || !$baseUri) {
            throw new RuntimeException('Mobile-money name lookup is temporarily unavailable. Please contact support.');
        }

        $response = Http::acceptJson()
            ->asJson()
            ->withToken($token)
            ->timeout(12)
            ->post($baseUri . '/resolve/mobile-money', [
                'phone' => $phone,
                'operator' => $operator,
                'country' => 'zm',
            ]);

        if (!$response->successful()) {
            throw new RuntimeException('We could not verify this mobile-money number. Check the number and try again.');
        }

        $payload = $response->json();
        if (!is_array($payload) || !($payload['status'] ?? false)) {
            throw new RuntimeException($payload['message'] ?? 'We could not verify this mobile-money number. Check the number and try again.');
        }

        $details = $payload['data'] ?? [];
        $name = is_array($details)
            ? ($details['name'] ?? $details['accountName'] ?? $details['account_name'] ?? $details['customerName'] ?? null)
            : null;

        if (!is_string($name) || trim($name) === '') {
            throw new RuntimeException('Lenco did not return an account name for this number. Check the number and try again.');
        }

        return [
            'phone' => $phone,
            'operator' => $operator,
            'account_name' => trim($name),
        ];
    }
}
