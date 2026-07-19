<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateController extends Controller
{
    /**
     * Fetch live exchange rates from OpenNode API
     */
    public function getRates()
    {
        try {
            $token = config('services.opennode.exchange_rates_token');
            
            if (!$token) {
                Log::error('Exchange rates token not configured');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Exchange rates service not configured',
                ], 500);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get('https://api.opennode.com/v1/rates');

            if ($response->successful()) {
                $data = $response->json();
                
                // OpenNode returns rates in format: { "data": { "BTCZMW": { "ZMW": 2101052.9, "BTC": 4.8e-7, ... }, ... } }
                // Extract ZMW rate from BTCZMW entry
                $btcZmwRate = null;
                $btcUsdRate = null;
                
                if (isset($data['data']['BTCZMW']['ZMW'])) {
                    $btcZmwRate = (float) $data['data']['BTCZMW']['ZMW'];
                }
                
                // Also get USD rate for fallback
                if (isset($data['data']['BTCUSD']['USD'])) {
                    $btcUsdRate = (float) $data['data']['BTCUSD']['USD'];
                }
                
                return response()->json([
                    'status' => 'success',
                    'btc_zmw' => $btcZmwRate,
                    'btc_usd' => $btcUsdRate,
                    'buy_service_fee_rate' => config('services.bitcoin.service_fee_rate'),
                    'sell_service_fee_rate' => config('services.bitcoin.sell_service_fee_rate'),
                    'buy_network_fee' => config('services.bitcoin.buy_network_fee'),
                    'sell_network_fee' => config('services.bitcoin.sell_network_fee'),
                ]);
            } else {
                Log::error('OpenNode rates API error: ' . $response->body());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to fetch exchange rates',
                    'error' => $response->json(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Error fetching exchange rates: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching exchange rates',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Check OpenNode account balance and validate if sufficient for purchase
     */
    public function checkBalance(Request $request)
    {
        try {
            $token = config('services.opennode.balance_check_token');
            
            if (!$token) {
                Log::error('Balance check token not configured');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Balance check service not configured',
                ], 500);
            }

            $request->validate([
                'amount_sats' => 'required|numeric|min:1',
            ]);

            $amountSats = (float) $request->input('amount_sats');
            $amountBtc = $amountSats / 100000000; // Convert SATS to BTC

            // Fetch balance from OpenNode
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get('https://api.opennode.com/v1/account/balance');

            if ($response->successful()) {
                $data = $response->json();
                // Note: OpenNode returns balance in SATS, not BTC, even though field is labeled "BTC"
                $balanceSats = (float) ($data['data']['balance']['BTC'] ?? 0);
                $balanceBtc = $balanceSats / 100000000; // Convert SATS to BTC
                
                $isSufficient = $balanceSats >= $amountSats;
                
                return response()->json([
                    'status' => 'success',
                    'sufficient' => $isSufficient,
                    'balance_btc' => $balanceBtc,
                    'required_btc' => $amountBtc,
                    'balance_sats' => $balanceSats,
                    'required_sats' => $amountSats,
                ]);
            } else {
                Log::error('OpenNode balance API error: ' . $response->body());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to check balance',
                    'error' => $response->json(),
                ], $response->status());
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error checking balance: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while checking balance',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get current OpenNode account balance (without validation)
     */
    public function getBalance()
    {
        try {
            $token = config('services.opennode.balance_check_token');
            
            if (!$token) {
                Log::error('Balance check token not configured');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Balance check service not configured',
                ], 500);
            }

            // Fetch balance from OpenNode
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get('https://api.opennode.com/v1/account/balance');

            if ($response->successful()) {
                $data = $response->json();
                // Note: OpenNode returns balance in SATS, not BTC, even though field is labeled "BTC"
                $balanceSats = (float) ($data['data']['balance']['BTC'] ?? 0);
                $balanceBtc = $balanceSats / 100000000; // Convert SATS to BTC
                
                return response()->json([
                    'status' => 'success',
                    'balance_btc' => $balanceBtc,
                    'balance_sats' => $balanceSats,
                    'raw_response' => $data, // Include raw response for debugging
                ]);
            } else {
                Log::error('OpenNode balance API error: ' . $response->body());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to fetch balance',
                    'error' => $response->json(),
                    'raw_response' => $response->body(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Error fetching balance: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching balance',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}

