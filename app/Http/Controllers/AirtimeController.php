<?php

namespace App\Http\Controllers;

use App\Models\AirtimePurchase;
use App\Services\OpenNodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AirtimeController extends Controller
{
    public function generateInvoice(Request $request)
    {
        try {
            $data = $request->validate([
                'phone' => 'required|string',
                'amount_kwacha' => 'required|numeric|min:5|max:100',
                'amount_sats' => 'required|numeric|min:1',
                'amount_btc' => 'required|numeric',
                'total_sats' => 'required|numeric|min:1',
            ]);

            $data['conversion_fee'] = 0;
            $data['network_fee'] = 0;
            $data['total_sats'] = (int) round($data['amount_sats']);

            $apiKey = config('services.opennode.api_key');
            $callbackUrl = config('services.opennode.airtime');

            if (!$apiKey) {
                Log::error('OpenNode configuration missing for airtime purchase');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment gateway configuration error. Please contact support.',
                ], 500);
            }

            if (!$callbackUrl) {
                Log::error('OpenNode airtime webhook URL is not configured');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Airtime payment webhook is not configured. Please contact support.',
                ], 500);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post(OpenNodeService::chargesUrl(), OpenNodeService::chargePayload([
                'amount' => (int) $data['total_sats'],
                'description' => 'Airtime purchase - ' . $data['phone'],
                'customer_name' => 'Customer',
                'customer_email' => 'customer@bitkwik.com',
                'order_id' => 'airtime_' . time(),
            ], $callbackUrl));

            if (!$response->successful()) {
                Log::error('OpenNode airtime invoice generation failed: ' . $response->body());
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

            $logoPath = public_path('ui/css/assets/img/logo.png');
            $processedLogoPath = $this->addRoundedCorners($logoPath);

            $qrCodeImage = QrCode::format('png')
                ->size(400)
                ->merge($processedLogoPath, .17, true)
                ->generate($bolt11);

            $qrCodeDir = public_path('images/qrcodes');
            if (!file_exists($qrCodeDir)) {
                mkdir($qrCodeDir, 0755, true);
            }

            $qrCodeFileName = 'airtime_' . time() . '.png';
            file_put_contents($qrCodeDir . '/' . $qrCodeFileName, $qrCodeImage);

            if ($processedLogoPath !== $logoPath && file_exists($processedLogoPath)) {
                unlink($processedLogoPath);
            }

            AirtimePurchase::create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'mobile_number' => $data['phone'],
                'amount_kwacha' => $data['amount_kwacha'],
                'amount_sats' => $data['amount_sats'],
                'amount_btc' => $data['amount_btc'],
                'convenience_fee' => $data['conversion_fee'],
                'total_sats' => $data['total_sats'],
                'network_fee' => $data['network_fee'],
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
                'total_sats' => $data['total_sats'],
                'message' => 'Invoice generated successfully. Please scan the QR code to pay.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check your input.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error generating airtime invoice: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
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

        imagealphablending($image, false);
        imagesavealpha($image, true);

        $width = imagesx($image);
        $height = imagesy($image);
        $radius = min($radius, $width / 2, $height / 2);

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $inCorner = false;

                if ($x < $radius && $y < $radius) {
                    $distance = sqrt(($radius - $x) ** 2 + ($radius - $y) ** 2);
                    $inCorner = $distance > $radius;
                } elseif ($x >= $width - $radius && $y < $radius) {
                    $distance = sqrt(($x - ($width - $radius)) ** 2 + ($radius - $y) ** 2);
                    $inCorner = $distance > $radius;
                } elseif ($x < $radius && $y >= $height - $radius) {
                    $distance = sqrt(($radius - $x) ** 2 + ($y - ($height - $radius)) ** 2);
                    $inCorner = $distance > $radius;
                } elseif ($x >= $width - $radius && $y >= $height - $radius) {
                    $distance = sqrt(($x - ($width - $radius)) ** 2 + ($y - ($height - $radius)) ** 2);
                    $inCorner = $distance > $radius;
                }

                if ($inCorner) {
                    $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
                    imagesetpixel($image, $x, $y, $transparent);
                }
            }
        }

        $tempPath = sys_get_temp_dir() . '/logo_rounded_' . time() . '.png';
        imagepng($image, $tempPath);
        imagedestroy($image);

        return $tempPath;
    }
}
