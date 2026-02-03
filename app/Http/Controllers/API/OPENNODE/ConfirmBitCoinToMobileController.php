<?php

namespace App\Http\Controllers\API\OPENNODE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BitCoinToMobileMoney;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class ConfirmBitCoinToMobileController extends Controller
{


    public function confirmBitCoinToMobileMoneyPayments(Request $request)
    {
        try {
            Log::info('OPENNODE Webhook Raw:', $request->all());

            $data = $request->all();

            Log::info('OPENNODE Webhook Parsed:', $data);

            if (empty($data['id'])) {
                throw new \Exception('Missing id (used as checking_id?)');
            }


            $payment = BitCoinToMobileMoney::where('checking_id', $data['id'])
                ->first();

            if (!$payment) {
                Log::warning('Invalid checking_id in webhook', [
                    'checking_id' => $data['id']
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid checking_id',
                ], 400);
            }

            $isPaid = false;
            if (isset($data['status'])) {
                $isPaid = ($data['status'] === 'paid');
            }

            if ($payment->payment_status === 'paid') {
                Log::info('Payment already scheduled/paid, ignoring duplicate webhook', ['id' => $data['id']]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment already processed',
                ]);
            }

            $payment->update([
                'payment_status' => $isPaid ? 'paid' : 'pending',
                'paid_at' => $isPaid ? now() : null,
            ]);

            if ($isPaid) {
                $number = preg_replace('/\D/', '', $payment->mobile_number);
                $prefix = substr($number, 0, 3);

                $operator = match ($prefix) {
                    '097', '077' => 'airtel',
                    '096', '076' => 'mtn',
                    '095', '075' => 'zamtel',
                    default => 'unknown',
                };

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.lenco.token'),
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->post(config('services.lenco.base_uri') . '/transfers/mobile-money', [
                            'accountId' => config('services.lenco.wallet_uuid'),
                            'amount' => $payment->amount_kwacha,
                            'narration' => 'BitCoin Transfer to Mobile Money',
                            'reference' => $payment->id . '-' . mt_rand(),
                            'transferRecipientId' => '',
                            'phone' => $payment->mobile_number,
                            'operator' => $operator,
                            'country' => 'zm',
                        ]);

                if ($response->successful()) {
                    Log::info('Lenco Transfer OK:', $response->json());
                } else {
                    Log::error('Lenco Transfer Failed:', $response->json());
                }
            }

            return response()->json([
                'status' => 'success',
                'payment' => $payment,
            ]);
        } catch (\Throwable $e) {
            Log::error('API Error:', ['message' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
