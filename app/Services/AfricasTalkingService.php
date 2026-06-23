<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AfricasTalkingService
{
    public function formatPhoneNumber(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '260')) {
            return '+' . $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '+260' . substr($digits, 1);
        }

        if (strlen($digits) === 9) {
            return '+260' . $digits;
        }

        return '+' . $digits;
    }

    /**
     * Send airtime via Africa's Talking API.
     *
     * @return array{success: bool, data?: array, message?: string}
     */
    public function sendAirtime(string $phoneNumber, float $amountZmw, string $idempotencyKey): array
    {
        $apiKey = config('services.africastalking.api_key');
        $username = config('services.africastalking.username');
        $baseUri = config('services.africastalking.base_uri');

        if (!$apiKey || !$username || !$baseUri) {
            Log::error('Africa\'s Talking configuration missing');
            return [
                'success' => false,
                'message' => 'Airtime service not configured.',
            ];
        }

        $formattedPhone = $this->formatPhoneNumber($phoneNumber);
        $amount = sprintf('ZMW %.2f', $amountZmw);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
                'Idempotency-Key' => $idempotencyKey,
                'apiKey' => $apiKey,
            ])->post(rtrim($baseUri, '/') . '/airtime/send', [
                'username' => $username,
                'recipients' => [
                    [
                        'phoneNumber' => $formattedPhone,
                        'amount' => $amount,
                    ],
                ],
                'maxNumRetry' => 2,
            ]);

            $data = $response->json();

            if (!$response->successful()) {
                Log::error('Africa\'s Talking airtime API error', [
                    'status' => $response->status(),
                    'body' => $data,
                ]);

                return [
                    'success' => false,
                    'message' => $data['errorMessage'] ?? 'Failed to send airtime.',
                    'data' => $data,
                ];
            }

            $recipientResponse = $data['responses'][0] ?? null;
            $errorMessage = $recipientResponse['errorMessage'] ?? $data['errorMessage'] ?? 'None';
            $status = $recipientResponse['status'] ?? null;

            if ($errorMessage !== 'None' || ($status && strtolower($status) !== 'sent')) {
                Log::error('Africa\'s Talking airtime delivery failed', $data);

                return [
                    'success' => false,
                    'message' => $errorMessage !== 'None' ? $errorMessage : 'Airtime delivery failed.',
                    'data' => $data,
                ];
            }

            Log::info('Africa\'s Talking airtime sent successfully', $data);

            return [
                'success' => true,
                'data' => $data,
            ];
        } catch (\Throwable $e) {
            Log::error('Africa\'s Talking airtime exception: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
