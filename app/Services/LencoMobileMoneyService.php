<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class LencoMobileMoneyService
{
    private const UNREGISTERED_MESSAGE = 'Number not registered for mobile money.';

    /** Resolve the account holder name for a Zambian mobile-money number. */
    public function resolve(string $phone, ?string $operator = null): array
    {
        $phone = ZambianMobileNumber::normalize($phone);
        $operator = strtolower($operator ?: ZambianMobileNumber::operator($phone));

        if (!in_array($operator, ['mtn', 'airtel', 'zamtel'], true)) {
            throw new \\InvalidArgumentException('Choose a supported Zambian mobile-money operator.');
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

        $payload = $response->json();
        $apiMessage = is_array($payload) ? (string) ($payload['message'] ?? '') : '';
        if ($response->status() === 404 || preg_match('/not\\s+found|not\\s+registered|no\\s+account/i', $apiMessage)) {
            throw new RuntimeException(self::UNREGISTERED_MESSAGE);
        }

        if (!$response->successful()) {
            throw new RuntimeException('We could not verify this mobile-money number right now. Please try again.');
        }

        if (!is_array($payload) || !($payload['status'] ?? false)) {
            if (is_array($payload) && preg_match('/not\\s+found|not\\s+registered|no\\s+account/i', $apiMessage)) {
                throw new RuntimeException(self::UNREGISTERED_MESSAGE);
            }
            throw new RuntimeException(self::UNREGISTERED_MESSAGE);
        }

        $details = $payload['data'] ?? [];
        $name = is_array($details)
            ? ($details['name'] ?? $details['accountName'] ?? $details['account_name'] ?? $details['customerName'] ?? null)
            : null;

        if (!is_string($name) || trim($name) === '') {
            throw new RuntimeException(self::UNREGISTERED_MESSAGE);
        }

        return [
            'phone' => $phone,
            'operator' => $operator,
            'account_name' => trim($name),
        ];
    }
}
