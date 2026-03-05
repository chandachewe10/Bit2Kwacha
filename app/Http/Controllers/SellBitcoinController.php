<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\BitCoinToMobileMoney;


class SellBitcoinController extends Controller
{
    /**
     * Check Lenco wallet balance to ensure we have enough ZMW float
     * to pay the user when they sell Bitcoin.
     *
     * @param float|null $requiredAmount  Optional ZMW amount to validate against
     * @return array  ['status' => 'success'|'error', 'available_balance' => float, 'sufficient' => bool, ...]
     */
    public function checkLencoFloat($requiredAmount = null)
    {
        try {
            $token = config('services.lenco.token');
            $walletUuid = config('services.lenco.wallet_uuid');
            $baseUri = config('services.lenco.base_uri');

            if (!$token || !$walletUuid || !$baseUri) {
                Log::error('Lenco configuration missing for balance check');
                return [
                    'status' => 'error',
                    'message' => 'Float check service not configured. Please contact support.',
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get("{$baseUri}/accounts/{$walletUuid}/balance");

            if (!$response->successful()) {
                Log::error('Lenco balance API error: ' . $response->body());
                return [
                    'status' => 'error',
                    'message' => 'Unable to verify available float. Please try again later.',
                    'http_status' => $response->status(),
                ];
            }

            $data = $response->json();

            if (!($data['status'] ?? false)) {
                Log::error('Lenco balance API returned unsuccessful status: ' . json_encode($data));
                return [
                    'status' => 'error',
                    'message' => 'Unable to verify available float. Please try again later.',
                ];
            }

            $availableBalance = (float) ($data['data']['availableBalance'] ?? 0);
            $currency = $data['data']['currency'] ?? 'ZMW';

            $result = [
                'status' => 'success',
                'available_balance' => $availableBalance,
                'currency' => $currency,
            ];

            if ($requiredAmount !== null) {
                $result['sufficient'] = $availableBalance >= $requiredAmount;
                $result['required_amount'] = $requiredAmount;
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Error checking Lenco balance: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'An error occurred while checking float. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }

    /**
     * API endpoint to check Lenco float balance (for client-side pre-check)
     */
    public function checkFloat(Request $request)
    {
        $requiredAmount = $request->input('amount_kwacha') ? (float) $request->input('amount_kwacha') : null;
        $result = $this->checkLencoFloat($requiredAmount);

        $statusCode = $result['status'] === 'success' ? 200 : 500;
        return response()->json($result, $statusCode);
    }

    public function generateInvoice(Request $request)
    {
        try {
            // Validate request data
            $data = $request->validate([
                'phone' => 'required|string',
                'amount_sats' => 'required|numeric|min:500',
                'amount_btc' => 'required|numeric',
                'amount_kwacha' => 'required|numeric',
                'conversion_fee' => 'required|numeric',
                'total_sats' => 'required|numeric|min:500',
                'network_fee' => 'required|numeric',
            ]);

            // ── Check Lenco wallet float before proceeding ──
            $floatCheck = $this->checkLencoFloat((float) $data['amount_kwacha']);

            if ($floatCheck['status'] !== 'success') {
                return response()->json([
                    'status' => 'error',
                    'message' => $floatCheck['message'] ?? 'Unable to verify float. Please try again later.',
                ], 503);
            }

            if (!($floatCheck['sufficient'] ?? false)) {
                $available = number_format($floatCheck['available_balance'] ?? 0, 2);
                Log::warning('Insufficient Lenco float for sell. Required: ' . $data['amount_kwacha'] . ' ZMW, Available: ' . $available . ' ZMW');
                return response()->json([
                    'status' => 'error',
                    'message' => "We've temporarily run out of float. Please try again later or contact support.",
                    'insufficient_float' => true,
                ], 200);
            }

            // Check OpenNode configuration
            $apiKey = config('services.opennode.api_key');
            $baseUri = config('services.opennode.base_uri');

            if (!$apiKey || !$baseUri) {
                Log::error('OpenNode configuration missing');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment gateway configuration error. Please contact support.',
                ], 500);
            }

            // Generate Lightning invoice using OpenNode
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post($baseUri . '/charges', [
                        'amount' => (int) $data['total_sats'],
                        'description' => 'Bitcoin to Kwacha - ' . $data['phone'],
                        'customer_name' => 'Customer',
                        'customer_email' => 'customer@bitkwik.com',
                        'order_id' => 'sell_' . time(),
                        'callback_url' => config('services.opennode.mobile_money'),
                        'success_url' => env('APP_URL'),
                        'auto_settle' => true,
                        'ttl' => 10,
                    ]);

            if (!$response->successful()) {
                Log::error('OpenNode invoice generation failed: ' . $response->body());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to generate invoice. Please try again.',
                    'details' => $response->json(),
                ], 400);
            }

            $json = $response->json()['data'] ?? [];
            $bolt11 = $json['lightning_invoice']['payreq'] ?? null;
            $checkingId = $json['id'] ?? null;

            if (!$bolt11) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invoice generation failed. Please try again.',
                ], 400);
            }

            // Generate QR code with logo
            $logoPath = public_path('ui/css/assets/img/logo.png');
            // Process logo to add rounded corners
            $processedLogoPath = $this->addRoundedCorners($logoPath);

            $qrCodeImage = QrCode::format('png')
                ->size(400)
                ->merge($processedLogoPath, .17, true)
                ->generate($bolt11);
            $qrCodeDir = public_path('images/qrcodes');

            // Ensure directory exists
            if (!file_exists($qrCodeDir)) {
                mkdir($qrCodeDir, 0755, true);
            }

            $qrCodeFileName = 'sell_bitcoin_' . time() . '.png';
            $filePath = $qrCodeDir . '/' . $qrCodeFileName;
            file_put_contents($filePath, $qrCodeImage);

            // Clean up temporary logo file
            if ($processedLogoPath !== $logoPath && file_exists($processedLogoPath)) {
                unlink($processedLogoPath);
            }

            // Save transaction to database
            BitCoinToMobileMoney::create([
                "user_id" => auth()->check() ? auth()->id() : null,
                "amount_kwacha" => $data['amount_kwacha'],
                "amount_sats" => $data['amount_sats'],
                "amount_btc" => $data['amount_btc'],
                "network_fee" => $data['network_fee'],
                "total_sats" => $data['total_sats'],
                "mobile_number" => $data['phone'],
                "convenience_fee" => $data['conversion_fee'],
                "customer_name" => 'Customer',
                "customer_phone" => $data['phone'],
                "delivery_email" => 'customer@bitkwik.com',
                'qr_code_path' => $qrCodeFileName,
                'lightning_invoice_address' => $bolt11,
                'checking_id' => $checkingId,
                'checkout_url' => $json['hosted_checkout_url'] ?? null,
                'payment_status' => 'pending',
            ]);

            return response()->json([
                'status' => 'success',
                'bolt11' => $bolt11,
                'qr_code_path' => $qrCodeFileName,
                'qr_code_url' => asset('images/qrcodes/' . $qrCodeFileName),
                'checking_id' => $checkingId,
                'amount_kwacha' => $data['amount_kwacha'],
                'amount_sats' => $data['amount_sats'],
                'message' => 'Invoice generated successfully. Please scan the QR code to pay.',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check your input.',
                'error' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error generating invoice: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            $errorMessage = config('app.debug')
                ? $e->getMessage() . ' (File: ' . basename($e->getFile()) . ', Line: ' . $e->getLine() . ')'
                : 'An error occurred. Please try again.';

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred. Please try again.',
                'error' => $errorMessage,
                'details' => config('app.debug') ? [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ] : null,
            ], 500);
        }
    }

    private function addRoundedCorners($logoPath, $radius = 20)
    {
        if (!file_exists($logoPath) || !function_exists('imagecreatefrompng')) {
            return $logoPath;
        }

        $image = imagecreatefrompng($logoPath);
        if (!$image) {
            return $logoPath;
        }

        // Enable alpha blending and save alpha
        imagealphablending($image, false);
        imagesavealpha($image, true);

        // Get image dimensions
        $width = imagesx($image);
        $height = imagesy($image);

        // Adjust radius if it's too large
        $radius = min($radius, $width / 2, $height / 2);

        // Create mask for rounded corners
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                // Check if pixel is in corner regions
                $inCorner = false;

                // Top-left corner
                if ($x < $radius && $y < $radius) {
                    $dx = $radius - $x;
                    $dy = $radius - $y;
                    $distance = sqrt($dx * $dx + $dy * $dy);
                    if ($distance > $radius) {
                        $inCorner = true;
                    }
                }
                // Top-right corner
                elseif ($x >= $width - $radius && $y < $radius) {
                    $dx = $x - ($width - $radius);
                    $dy = $radius - $y;
                    $distance = sqrt($dx * $dx + $dy * $dy);
                    if ($distance > $radius) {
                        $inCorner = true;
                    }
                }
                // Bottom-left corner
                elseif ($x < $radius && $y >= $height - $radius) {
                    $dx = $radius - $x;
                    $dy = $y - ($height - $radius);
                    $distance = sqrt($dx * $dx + $dy * $dy);
                    if ($distance > $radius) {
                        $inCorner = true;
                    }
                }
                // Bottom-right corner
                elseif ($x >= $width - $radius && $y >= $height - $radius) {
                    $dx = $x - ($width - $radius);
                    $dy = $y - ($height - $radius);
                    $distance = sqrt($dx * $dx + $dy * $dy);
                    if ($distance > $radius) {
                        $inCorner = true;
                    }
                }

                // Make corner pixels transparent
                if ($inCorner) {
                    $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
                    imagesetpixel($image, $x, $y, $transparent);
                }
            }
        }

        // Save processed logo to temporary file
        $tempPath = sys_get_temp_dir() . '/logo_rounded_' . time() . '.png';
        imagepng($image, $tempPath);
        imagedestroy($image);

        return $tempPath;
    }
}

