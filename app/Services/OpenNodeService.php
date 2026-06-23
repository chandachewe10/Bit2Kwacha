<?php

namespace App\Services;

class OpenNodeService
{
    public static function chargesUrl(): string
    {
        $baseUri = config('services.opennode.base_uri');

        if (!str_starts_with($baseUri, 'http')) {
            $baseUri = 'https://' . ltrim($baseUri, '/');
        }

        return rtrim($baseUri, '/') . '/charges';
    }

    /**
     * OpenNode rejects localhost URLs in success_url with HTTP 403.
     */
    public static function successUrl(): ?string
    {
        $url = config('services.opennode.success_url') ?? config('app.url');

        if (!$url || preg_match('/(localhost|127\.0\.0\.1)/i', $url)) {
            return null;
        }

        return $url;
    }

    public static function chargePayload(array $fields, string $callbackUrl): array
    {
        $payload = array_merge($fields, [
            'callback_url' => $callbackUrl,
            'auto_settle' => true,
            'ttl' => 10,
        ]);

        if ($successUrl = self::successUrl()) {
            $payload['success_url'] = $successUrl;
        }

        return $payload;
    }
}
