<?php

namespace App\Http\Controllers\API\OPENNODE;

use App\Http\Controllers\Controller;
use App\Models\AirtimePurchase;
use App\Services\AfricasTalkingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConfirmBitCoinToAirtimeController extends Controller
{
    public function confirmBitCoinToAirtimePayments(Request $request, AfricasTalkingService $africasTalking)
    {
        try {
            Log::info('OPENNODE Airtime Webhook Raw:', $request->all());

            $data = $request->all();

            if (empty($data['id'])) {
                throw new \Exception('Missing id (checking_id)');
            }

            $payment = AirtimePurchase::where('checking_id', $data['id'])->first();

            if (!$payment) {
                $status = $data['status'] ?? null;

                if ($status !== 'paid') {
                    Log::info('Airtime webhook for unknown charge (non-paid), ignoring', [
                        'checking_id' => $data['id'],
                        'status' => $status,
                        'order_id' => $data['order_id'] ?? null,
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'No matching purchase; non-paid status ignored',
                    ]);
                }

                Log::error('Paid airtime webhook with no matching purchase', [
                    'checking_id' => $data['id'],
                    'order_id' => $data['order_id'] ?? null,
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid checking_id',
                ], 400);
            }

            $isPaid = isset($data['status']) && $data['status'] === 'paid';

            if ($payment->payment_status === 'paid' && $payment->airtime_status === 'sent') {
                Log::info('Airtime payment already processed, ignoring duplicate webhook', [
                    'id' => $data['id'],
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment already processed',
                ]);
            }

            $payment->update([
                'payment_status' => $isPaid ? 'paid' : 'pending',
                'paid_at' => $isPaid ? now() : null,
            ]);

            if ($isPaid && $payment->airtime_status !== 'sent') {
                $idempotencyKey = 'airtime-' . $payment->id . '-' . $payment->checking_id;

                $result = $africasTalking->sendAirtime(
                    $payment->mobile_number,
                    (float) $payment->amount_kwacha,
                    $idempotencyKey
                );

                $responseData = $result['data'] ?? [];
                $recipientResponse = $responseData['responses'][0] ?? [];

                $payment->update([
                    'airtime_status' => $result['success'] ? 'sent' : 'failed',
                    'airtime_request_id' => $recipientResponse['requestId'] ?? null,
                    'airtime_response' => $responseData ?: ['error' => $result['message'] ?? 'Unknown error'],
                ]);

                if (!$result['success']) {
                    Log::error('Airtime delivery failed after payment', [
                        'payment_id' => $payment->id,
                        'message' => $result['message'] ?? null,
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'payment' => $payment->fresh(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Airtime webhook error:', ['message' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
